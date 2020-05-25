<?php 
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutSpecificStrategy;

use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Repository\AbstractActivityRepository;
use App\Repository\MovementActivityRepository;
use App\Entity\AbstractActivity;
use Psr\Log\LoggerInterface;

class MovementSetWorkoutStrategy implements SpecificStrategyInterface
{

    private $activityRepository;

    private $movementRepository;

    private $logger;

    public function __construct(AbstractActivityRepository $activityRepository, MovementActivityRepository $movementRepository, LoggerInterface $logger)
    {
        $this->activityRepository = $activityRepository;
        $this->movementRepository = $movementRepository;
        $this->logger = $logger;
    }

    public function fill(AbstractWorkoutFormModel $workoutModel): ?AbstractWorkoutFormModel
    {

        $durationSecondsTotal = 0;
        $burnoutEnergyTotal = 0;
        $distanceTotal = 0;

        $movementSetCollection = $workoutModel->getMovementSets();
        foreach ($movementSetCollection as $movementSet) {
            $activity = $this->getMovementActivity(
                $movementSet->getActivityName(),
                $movementSet->getAverageSpeed()
            );
            if ($activity) {
                $movementSet->setActivity($activity);
                $movementSet->calculateSaveBurnoutEnergy();
                $durationSecondsTotal += $movementSet->getDurationSeconds();
                $burnoutEnergyTotal += $movementSet->getBurnoutEnergy();
                $distanceTotal += $movementSet->getDistance();
            } else {
                $message = sprintf('Data about activity with name "%s" and average speed "%s" do not exist in our database. Contact with admin.',$movementSet->getActivityName(), $movementSet->getAverageSpeed());
                $this->logger->alert($message);
                //throw new \Exception($message);
                return null;
            }
        }

        $workoutActivity = $this->activityRepository->findOneBy([
            'name' => $workoutModel->getActivityName()
        ]);
        
        $workoutModel
            ->setActivity($workoutActivity)
            ->setDurationSecondsTotal($durationSecondsTotal)
            ->setBurnoutEnergyTotal($burnoutEnergyTotal)
            ->setDistanceTotal($distanceTotal)
            ;
         
        return $workoutModel;

    }

    /**
     * getMovementActivity Get movement activity by given name and average speed 
     * @param  string $activityName Name of activity
     * @param  float    $averageSpeed Average speed
     * @return AbstractActivity|null
     */
    private function getMovementActivity(string $activityName, float $averageSpeed): ?AbstractActivity
    {
        return $this->movementRepository->findOneActivityBySpeedAverageAndName(
            $activityName,
            $averageSpeed
        );
    }

}

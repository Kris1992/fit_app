<?php 
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutSpecificStrategy;

use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Repository\MovementActivityRepository;
use App\Entity\AbstractActivity;
use Psr\Log\LoggerInterface;

class MovementWorkoutStrategy implements SpecificStrategyInterface
{

    private $movementRepository;

    private $logger;

    public function __construct(MovementActivityRepository $movementRepository, LoggerInterface $logger)
    {
        $this->movementRepository = $movementRepository;
        $this->logger = $logger;
    }

    public function fill(AbstractWorkoutFormModel $workoutModel): ?AbstractWorkoutFormModel
    {

        $activity = $this->getMovementActivity(
            $workoutModel->getActivityName(),
            $workoutModel->getAverageSpeed()
        );

        if ($activity) {
            $workoutModel                    
                ->setActivity($activity)
                ->calculateSaveBurnoutEnergyTotal()
                ;

            return $workoutModel;
        }

        $this->logger->alert(sprintf('Workout specific extender catched try of expend unsupported activity with name "%s" and average speed "%s" !!',$workoutModel->getActivityName(), $workoutModel->getAverageSpeed()));

        return null;
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

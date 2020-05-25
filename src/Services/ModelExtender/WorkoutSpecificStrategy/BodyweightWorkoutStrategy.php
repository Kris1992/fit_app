<?php 
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutSpecificStrategy;

use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Repository\BodyweightActivityRepository;
use App\Entity\AbstractActivity;
use Psr\Log\LoggerInterface;

class BodyweightWorkoutStrategy implements SpecificStrategyInterface
{

    private $bodyweightRepository;

    private $logger;

    public function __construct(BodyweightActivityRepository $bodyweightRepository, LoggerInterface $logger)
    {
        $this->bodyweightRepository = $bodyweightRepository;
        $this->logger = $logger;
    }

    public function fill(AbstractWorkoutFormModel $workoutModel): ?AbstractWorkoutFormModel
    {
        
       $activity = $this->getBodyweightActivity(
            $workoutModel->getActivityName(),
            $workoutModel->getRepetitionsPerHour()
        );

        if ($activity) {
            $workoutModel                    
                ->setActivity($activity)
                ->calculateSaveBurnoutEnergyTotal()
                ;

            return $workoutModel;
        }

        $this->logger->alert(sprintf('Workout specific extender catched try of expend unsupported activity with name "%s" and average repetitions "%d" !!',$workoutModel->getActivityName(), $workoutModel->getRepetitionsPerHour()));

        return null;

    }

    /**
     * getBodyweightActivity Get bodyweight activity by given name and repetitions by hour 
     * @param  string $activityName Name of activity
     * @param  int    $repetitionsPerHour Average repetitions per hour
     * @return AbstractActivity|null
     */
    private function getBodyweightActivity(string $activityName, int $repetitionsPerHour): ?AbstractActivity
    {
        return $this->bodyweightRepository->findOneActivityByRepetitionsPerHourAndName(
            $activityName,
            $repetitionsPerHour
        );
    }

}

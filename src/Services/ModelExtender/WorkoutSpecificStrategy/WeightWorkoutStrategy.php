<?php 
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutSpecificStrategy;

use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Repository\WeightActivityRepository;
use App\Entity\AbstractActivity;
use Psr\Log\LoggerInterface;

class WeightWorkoutStrategy implements SpecificStrategyInterface
{

    private $weightRepository;

    private $logger;

    public function __construct(WeightActivityRepository $weightRepository, LoggerInterface $logger)
    {
        $this->weightRepository = $weightRepository;
        $this->logger = $logger;
    }

    public function fill(AbstractWorkoutFormModel $workoutModel): ?AbstractWorkoutFormModel
    {
        $activity = $this->getWeightActivity(
            $workoutModel->getActivityName(),
            $workoutModel->getRepetitionsPerHour(),
            $workoutModel->getDumbbellWeight()
        );

        if ($activity) {
            $workoutModel                    
                ->setActivity($activity)
                ->calculateSaveBurnoutEnergyTotal()
                ;

            return $workoutModel;
        }

        $this->logger->alert(sprintf('Workout specific extender catched try of expend unsupported activity with name "%s" and average repetitions "%d"  and weight "%d"!!',$workoutModel->getActivityName(), $workoutModel->getRepetitionsPerHour(), $workoutModel->getDumbbellWeight()));

        return null;

    }

    /**
     * getWeightActivity Get weight activity by given name and repetitions by hour and dumbbell weight 
     * @param  string $activityName Name of activity
     * @param  int    $repetitionsPerHour Average repetitions per hour
     * @param  float    $dumbbellWeight Average weight of dumbbell
     * @return AbstractActivity|null
     */
    private function getWeightActivity(string $activityName, int $repetitionsPerHour, float $dumbbellWeight): ?AbstractActivity
    {
        return $this->weightRepository->findOneActivityByRepetitionsPerHourAndWeightAndName(
            $activityName,
            $repetitionsPerHour,
            $dumbbellWeight
        );
    }

}

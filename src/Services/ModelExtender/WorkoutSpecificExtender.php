<?php

namespace App\Services\ModelExtender;

use App\Entity\User;
use App\Entity\AbstractActivity;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Repository\MovementActivityRepository;
use App\Repository\AbstractActivityRepository;

class WorkoutSpecificExtender implements WorkoutExtenderInterface {
    
    private $movementRepository;
    private $activityRepository;

    public function __construct(
        MovementActivityRepository $movementRepository, 
        AbstractActivityRepository $activityRepository
    )
    {
        $this->movementRepository = $movementRepository;
        $this->activityRepository = $activityRepository;
    } 

    public function fillWorkoutModel(AbstractWorkoutFormModel $workoutModel, ?User $user): ?AbstractWorkoutFormModel
    {
        if ($user) {
            $workoutModel                    
                ->setUser($user);
        }

        switch ($workoutModel->getType()) {
            case 'Movement':
                return $this->fillMovementProperties($workoutModel);
            case 'MovementSet':
                return $this->fillMovementSetProperties($workoutModel);
        }

        return null;
    }

    private function fillMovementProperties(AbstractWorkoutFormModel $workoutModel): ?AbstractWorkoutFormModel
    {

        /*$activity = $this->movementRepository->findOneActivityBySpeedAverageAndName(
            $workoutModel->getActivityName(),
            $workoutModel->getAverageSpeed()
        );*/
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

        //logger here

        return null;
    }

    private function fillMovementSetProperties(AbstractWorkoutFormModel $workoutModel): AbstractWorkoutFormModel
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
                //logger i throw error
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
     * @param  int    $averageSpeed Average speed
     * @return AbstractActivity
     */
    private function getMovementActivity(string $activityName, int $averageSpeed): AbstractActivity
    {
        return $this->movementRepository->findOneActivityBySpeedAverageAndName(
            $activityName,
            $averageSpeed
        );
    }
}
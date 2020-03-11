<?php

namespace App\Services\ModelExtender;

use App\Entity\User;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Repository\MovementActivityRepository;

class WorkoutSpecificExtender implements WorkoutExtenderInterface {
    
    private $movementRepository;

    public function __construct(MovementActivityRepository $movementRepository)
    {
        $this->movementRepository = $movementRepository;
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
        }

        return null;
    }

    private function fillMovementProperties(AbstractWorkoutFormModel $workoutModel): ?AbstractWorkoutFormModel
    {

        $activity = $this->movementRepository->findOneActivityBySpeedAverageAndName(
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
}
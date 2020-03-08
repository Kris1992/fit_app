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

    public function fillWorkoutModel(AbstractWorkoutFormModel $workoutModel, User $user): AbstractWorkoutFormModel
    {
        switch ($workoutModel->getType()) {
            case 'Movement':
                $this->fillMovementProperties($workoutModel);
                break;
        }

        $workoutModel                    
            ->setUser($user);

        return $workoutModel;
    }

    private function fillMovementProperties(AbstractWorkoutFormModel $workoutModel): AbstractWorkoutFormModel
    {

        $activity = $this->movementRepository->findOneActivityBySpeedAverageAndName(
            $workoutModel->getActivityName(),
            $workoutModel->getAverageSpeed()
        );
        $workoutModel                    
            ->setActivity($activity)
            ->calculateSaveBurnoutEnergyTotal()
            ;

        return $workoutModel;
    }
}
<?php

namespace App\Services\ModelExtender;

use App\Entity\User;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

class WorkoutAverageExtender implements WorkoutExtenderInterface {

    public function fillWorkoutModel(AbstractWorkoutFormModel $workoutModel, User $user): AbstractWorkoutFormModel
    {
        $activity = $workoutModel->getActivity();

        
        switch ($activity->getType()) {
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

        $workoutModel
            ->calculateSaveBurnoutEnergyTotal()
            ->calculateSaveDistanceTotal()
            ;

        return $workoutModel;
    }
}
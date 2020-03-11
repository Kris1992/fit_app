<?php

namespace App\Services\ModelExtender;

use App\Entity\User;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

class WorkoutAverageExtender implements WorkoutExtenderInterface {

    public function fillWorkoutModel(AbstractWorkoutFormModel $workoutModel, ?User $user): ?AbstractWorkoutFormModel
    {

        $activity = $workoutModel->getActivity();
        if ($user) {
            $workoutModel                    
                ->setUser($user);
        }
        
        switch ($activity->getType()) {
            case 'Movement':
                return $this->fillMovementProperties($workoutModel);
        }

        return null;
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
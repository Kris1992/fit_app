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
            case 'MovementSet':
                return $this->fillMovementSetProperties($workoutModel);
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

    private function fillMovementSetProperties(AbstractWorkoutFormModel $workoutModel): AbstractWorkoutFormModel
    {
        $durationSecondsTotal = 0;
        $burnoutEnergyTotal = 0;
        $distanceTotal = 0;

        $movementSetCollection = $workoutModel->getMovementSets();
        foreach ($movementSetCollection as $movementSet) {
            $movementSet
                ->calculateSaveBurnoutEnergy()
                ->calculateSaveDistance()
                ;
            $durationSecondsTotal += $movementSet->getDurationSeconds();
            $burnoutEnergyTotal += $movementSet->getBurnoutEnergy();
            $distanceTotal += $movementSet->getDistance();
        }
        
        $workoutModel
            ->setDurationSecondsTotal($durationSecondsTotal)
            ->setBurnoutEnergyTotal($burnoutEnergyTotal)
            ->setDistanceTotal($distanceTotal)
            ;
            
        return $workoutModel;
    }
}
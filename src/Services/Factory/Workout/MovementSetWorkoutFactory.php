<?php

namespace App\Services\Factory\Workout;

use App\Entity\Workout;
use App\Entity\MovementSet;

use App\Form\Model\Workout\AbstractWorkoutFormModel;

class MovementSetWorkoutFactory implements WorkoutFactoryInterface {

    public function create(AbstractWorkoutFormModel $workoutModel): Workout
    {

        $workout = new Workout();
        $workout
            ->setUser($workoutModel->getUser())
            ->setActivity($workoutModel->getActivity())
            ->setDurationSecondsTotal($workoutModel->getDurationSecondsTotal())
            ->setDistanceTotal($workoutModel->getDistanceTotal())
            ->setBurnoutEnergyTotal($workoutModel->getBurnoutEnergyTotal())
            ->setStartAt($workoutModel->getStartAt())
            ;

        $movementSetCollectionModel = $workoutModel->getMovementSets();
        foreach ($movementSetCollectionModel as $movementSetModel) {
            $movementSet = new MovementSet();
            $movementSet
                ->setWorkout($workout)
                ->setActivity($movementSetModel->getActivity())
                ->setDistance($movementSetModel->getDistance())
                ->setDurationSeconds($movementSetModel->getDurationSeconds())
                ->setBurnoutEnergy($movementSetModel->getBurnoutEnergy())
                ;
            $workout->addMovementSet($movementSet);
        }

        return $workout;
    }
}
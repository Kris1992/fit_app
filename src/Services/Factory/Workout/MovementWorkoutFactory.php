<?php

namespace App\Services\Factory\Workout;

use App\Entity\Workout;

use App\Form\Model\Workout\AbstractWorkoutFormModel;

class MovementWorkoutFactory implements WorkoutFactoryInterface {

    public function createWorkout(AbstractWorkoutFormModel $workoutModel): Workout
    {
        $workout = new Workout();
        $workout
            ->setUser($workoutModel->getUser())
            ->setActivity($workoutModel->getActivity())
            ->setDurationSecondsTotal($workoutModel->getDurationSecondsTotal())
            ->setDistanceTotal($workoutModel->getDistanceTotal())
            ->setBurnoutEnergyTotal($workoutModel->getBurnoutEnergyTotal())//dodac tą metodę usunąć calculate
            ->setStartAt($workoutModel->getStartAt())
            ;

        return $workout;
    }
}
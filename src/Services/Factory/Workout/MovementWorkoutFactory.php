<?php

namespace App\Services\Factory\Workout;

use App\Entity\Workout;

use App\Form\Model\Workout\AbstractWorkoutFormModel;

class MovementWorkoutFactory implements WorkoutFactoryInterface {

    public function createWorkoutFromSpecific(AbstractWorkoutFormModel $workoutModel): Workout
    {
        $workout = new Workout();
        $workout
            ->setUser($workoutModel->getUser())
            ->setActivity($workoutModel->getActivity())
            ->setDurationSeconds($workoutModel->getDurationSecondsTotal())
            ->setDistance($workoutModel->getDistanceTotal())
            ->setBurnoutEnergyTotal($workoutModel->getBurnoutEnergyTotal())//dodac tą metodę usunąć calculate
            ->setStartAt($workoutModel->getStartAt())
            ;

        return $workout;
    }

    public function createWorkoutFromAverage(AbstractWorkoutFormModel $workoutModel): Workout
    {

    }
}
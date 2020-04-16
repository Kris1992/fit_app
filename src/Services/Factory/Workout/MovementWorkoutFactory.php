<?php

namespace App\Services\Factory\Workout;

use App\Entity\Workout;

use App\Form\Model\Workout\AbstractWorkoutFormModel;


class MovementWorkoutFactory implements WorkoutFactoryInterface {

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
            ->setImageFilename($workoutModel->getImageFilename())
            ;

        return $workout;
    }
}
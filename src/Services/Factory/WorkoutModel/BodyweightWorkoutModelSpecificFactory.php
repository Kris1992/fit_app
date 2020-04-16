<?php

namespace App\Services\Factory\WorkoutModel;

use App\Entity\Workout;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Form\Model\Workout\WorkoutSpecificFormModel;

/**
 * Creates models with specific data from bodyweight type of workout
 */
class BodyweightWorkoutModelSpecificFactory implements WorkoutModelFactoryInterface {

    public function create(Workout $workout): AbstractWorkoutFormModel
    {
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setId($workout->getId())
            ->setUser($workout->getUser())
            ->setActivityName($workout->getActivity()->getName())
            ->setDurationSecondsTotal($workout->getDurationSecondsTotal())
            ->setStartAt($workout->getStartAt())
            ->setType($workout->getActivity()->getType())
            ->setRepetitionsTotal($workout->getRepetitionsTotal())
            ->setImageFilename($workout->getImageFilename())
            ;

        return $workoutModel;
    }
}
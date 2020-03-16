<?php

namespace App\Services\Factory\WorkoutModel;

use App\Entity\Workout;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Form\Model\Workout\WorkoutAverageFormModel;

use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;

/**
 * Creates models with average data from movement set type workout
 */
class MovementSetWorkoutModelAverageFactory implements WorkoutModelFactoryInterface {

    public function create(Workout $workout): AbstractWorkoutFormModel
    {
        $workoutModel = new WorkoutAverageFormModel();
        $workoutModel
            ->setId($workout->getId())
            ->setUser($workout->getUser())
            ->setActivity($workout->getActivity())
            ->setDurationSecondsTotal($workout->getDurationSecondsTotal())
            ->setStartAt($workout->getStartAt())
            ->setType($workout->getActivity()->getType())
            ;

        $movementSetCollection = $workout->getMovementSets();
        foreach ($movementSetCollection as $movementSet) {
            $movementSetModel = new MovementActivitySetFormModel();
            $movementSetModel
                ->setId($movementSet->getId())
                ->setWorkout($workoutModel)
                ->setActivity($movementSet->getActivity())
                ->setDistance($movementSet->getDistance())
                ->setDurationSeconds($movementSet->getDurationSeconds())
                ->setBurnoutEnergy($movementSet->getBurnoutEnergy())
                ;
            $workoutModel->addMovementSet($movementSetModel);
        }

        return $workoutModel;
    }
}
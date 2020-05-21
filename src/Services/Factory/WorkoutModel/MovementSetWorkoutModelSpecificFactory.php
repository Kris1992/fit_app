<?php
declare(strict_types=1);

namespace App\Services\Factory\WorkoutModel;

use App\Entity\Workout;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Form\Model\Workout\WorkoutSpecificFormModel;

use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;

/**
 * Creates models with specific data from movement set type workout
 */
class MovementSetWorkoutModelSpecificFactory implements WorkoutModelFactoryInterface {

    public function create(Workout $workout): AbstractWorkoutFormModel
    {
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setId($workout->getId())
            ->setUser($workout->getUser())
            ->setActivityName($workout->getActivity()->getName())
            //->setDurationSecondsTotal($workout->getDurationSecondsTotal())
            ->setStartAt($workout->getStartAt())
            ->setType($workout->getActivity()->getType())
            ->setImageFilename($workout->getImageFilename())
            ;

        $movementSetCollection = $workout->getMovementSets();
        foreach ($movementSetCollection as $movementSet) {
            $movementSetModel = new MovementActivitySetFormModel();
            $movementSetModel
                ->setId($movementSet->getId())
                ->setWorkout($workoutModel)
                ->setActivityName($movementSet->getActivity()->getName())
                ->setDistance($movementSet->getDistance())
                ->setDurationSeconds($movementSet->getDurationSeconds())
                //->setBurnoutEnergy($movementSet->getBurnoutEnergy())
                ;
            $workoutModel->addMovementSet($movementSetModel);
        }

        return $workoutModel;
    }
}
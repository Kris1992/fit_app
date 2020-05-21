<?php
declare(strict_types=1);

namespace App\Services\Factory\Workout;

use App\Entity\Workout;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

class BodyweightWorkoutFactory implements WorkoutFactoryInterface {

    public function create(AbstractWorkoutFormModel $workoutModel): Workout
    {
        $workout = new Workout();
        $workout
            ->setUser($workoutModel->getUser())
            ->setActivity($workoutModel->getActivity())
            ->setDurationSecondsTotal($workoutModel->getDurationSecondsTotal())
            ->setRepetitionsTotal($workoutModel->getRepetitionsTotal())
            ->setBurnoutEnergyTotal($workoutModel->getBurnoutEnergyTotal())
            ->setStartAt($workoutModel->getStartAt())
            ->setImageFilename($workoutModel->getImageFilename())
            ;

        return $workout;
    }
}
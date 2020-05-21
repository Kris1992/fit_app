<?php
declare(strict_types=1);

namespace App\Services\Factory\Workout;

use App\Entity\Workout;
use App\Entity\AbstractActivity;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

class WeightWorkoutFactory implements WorkoutFactoryInterface {

    public function create(AbstractWorkoutFormModel $workoutModel): Workout
    {
        $workout = new Workout();
        $workout
            ->setUser($workoutModel->getUser())
            ->setActivity($workoutModel->getActivity())
            ->setDurationSecondsTotal($workoutModel->getDurationSecondsTotal())
            ->setStartAt($workoutModel->getStartAt())
            ->setBurnoutEnergyTotal($workoutModel->getBurnoutEnergyTotal())
            ->setRepetitionsTotal($workoutModel->getRepetitionsTotal())
            ->setDumbbellWeight($workoutModel->getDumbbellWeight())
            ->setImageFilename($workoutModel->getImageFilename())
            ;

        return $workout;
    }
}
<?php
declare(strict_types=1);

namespace App\Services\Factory\WorkoutModel;

use App\Entity\Workout;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Form\Model\Workout\WorkoutAverageFormModel;

/**
 * Creates models with average data from weight type workout
 */
class WeightWorkoutModelAverageFactory implements WorkoutModelFactoryInterface {

    public function create(Workout $workout): AbstractWorkoutFormModel
    {

        $workoutModel = new WorkoutAverageFormModel();
        $workoutModel
            ->setId($workout->getId())
            ->setUser($workout->getUser())
            ->setActivity($workout->getActivity())
            ->setDurationSecondsTotal($workout->getDurationSecondsTotal())
            ->setStartAt($workout->getStartAt())
            ->setImageFilename($workout->getImageFilename())
            ;

        return $workoutModel;
    }
}

            

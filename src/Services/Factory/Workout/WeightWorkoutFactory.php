<?php
//TO DO
namespace App\Services\Factory\Workout;

use App\Entity\Workout;
use App\Entity\AbstractActivity;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

class WeightWorkoutFactory implements WorkoutFactoryInterface {

    public function create(AbstractWorkoutFormModel $workoutModel): Workout
    {
        // naprawić
        $workout = new Workout();
        $workout
            ->setUser($activityArray['type'])
            ->setActivity($activityArray['name'])
            ->setBurnoutEnergyTotal($activityArray['energy'])//dodac tą metodę usunąć calculate
            ->setStartAt($activityArray['speedAverageMin'])
            ->setDurationSeconds($activityArray['speedAverageMax'])
            ->setDistance($activityArray['intensity'])
            ;

        return $workout;
    }
}
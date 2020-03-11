<?php
//TO DO
namespace App\Services\Factory\WorkoutModel;

use App\Entity\Workout;
use App\Entity\AbstractActivity;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

class WeightWorkoutModelAverageFactory implements WorkoutModelFactoryInterface {

    public function create(Workout $workout): AbstractWorkoutFormModel
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
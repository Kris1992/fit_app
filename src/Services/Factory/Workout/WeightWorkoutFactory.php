<?php
//TO DO
namespace App\Services\Factory\Workout;

use App\Entity\Workout;
use App\Entity\AbstractActivity;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

class WeightWorkoutFactory implements WorkoutFactoryInterface {
    
    public function createActivity(array $activityArray): AbstractActivity
    {
        $weightActivity = new WeightActivity();
        $weightActivity->setType($activityArray['type']);
        $weightActivity->setName($activityArray['name']);
        $weightActivity->setEnergy($activityArray['energy']);
        $weightActivity->setRepetitions($activityArray['repetitions']);
        $weightActivity->setWeight($activityArray['weight']);

        return $weightActivity;
    }

    public function createWorkoutFromSpecific(AbstractWorkoutFormModel $workoutModel): Workout
    {
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

    public function createWorkoutFromAverage(AbstractWorkoutFormModel $workoutModel): Workout
    {

    }
}
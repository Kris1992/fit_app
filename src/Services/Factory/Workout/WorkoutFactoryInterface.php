<?php

namespace App\Services\Factory\Workout;

use App\Entity\Workout;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
/**
 *  Manage creating of workouts
 */
interface WorkoutFactoryInterface
{   

    /**
     * createWorkoutFromSpecific  Create workout from specific workout data model 
     * @param  AbstractWorkoutFormModel  $workoutModel Model with workout data get from form
     * @return Workout Return workout
     */
    public function createWorkoutFromSpecific(AbstractWorkoutFormModel $workoutModel): Workout;

    /**
     * createWorkoutFromAverage  Create workout from average workout data model 
     * @param  AbstractWorkoutFormModel  $workoutModel Model with workout data get from form
     * @return Workout Return workout
     */
    public function createWorkoutFromAverage(AbstractWorkoutFormModel $workoutModel): Workout;

}
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
     * create  Create workout from workout model 
     * @param  AbstractWorkoutFormModel  $workoutModel Model with workout data get from form
     * @return Workout Return workout
     */
    public function create(AbstractWorkoutFormModel $workoutModel): Workout;

}
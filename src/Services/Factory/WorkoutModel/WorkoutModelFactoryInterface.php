<?php

namespace App\Services\Factory\WorkoutModel;

use App\Entity\Workout;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

/**
 *  Manage creating of workout models
 */
interface WorkoutModelFactoryInterface
{   

    /**
     * create  Create workout model from workout entity 
     * @param  Workout  $workout Entity with workout data get from db
     * @return AbstractWorkoutFormModel Return concrete workout model
     */
    public function create(Workout $workout): AbstractWorkoutFormModel;

}
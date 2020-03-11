<?php

namespace App\Services\ModelExtender;

use App\Entity\User;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

/**
 *  Manage filling missing data of model
 */
interface WorkoutExtenderInterface
{   

    /**
     * fillWorkoutModel  Extends workout model by fill missing data
     * @param  AbstractWorkoutFormModel  $workoutModel Model with workout data get from form
     * @param  User $user[optional] User object whose is owner of workout
     * @return AbstractWorkoutFormModel|null Return filled up workout model or null if workout type is 
     * not supported or activity doesn't exist in db 
     */
    public function fillWorkoutModel(AbstractWorkoutFormModel $workoutModel, ?User $user): ?AbstractWorkoutFormModel;
}
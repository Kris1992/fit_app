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
     * @return AbstractWorkoutFormModel Return filled up workout model
     */
    public function fillWorkoutModel(AbstractWorkoutFormModel $workoutModel, User $user): AbstractWorkoutFormModel;
}
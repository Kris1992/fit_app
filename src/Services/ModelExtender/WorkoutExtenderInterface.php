<?php
declare(strict_types=1);

namespace App\Services\ModelExtender;

use App\Entity\User;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
use Symfony\Component\HttpFoundation\File\File;

/**
 *  Manage filling missing data of model
 */
interface WorkoutExtenderInterface
{   

    /**
     * fillWorkoutModel  Extends workout model by fill missing data
     * @param  AbstractWorkoutFormModel  $workoutModel Model with workout data get from form
     * @param  User $user[optional] User object whose is owner of workout
     * @param  File $image[optional] Uploaded image of workout (uploaded by user or map with route)
     * @return AbstractWorkoutFormModel|null Return filled up workout model or null if workout type is 
     * not supported or activity doesn't exist in db 
     */
    public function fillWorkoutModel(AbstractWorkoutFormModel $workoutModel, ?User $user, ?File $image): ?AbstractWorkoutFormModel;

    /**
     * fillWorkoutModelWithMap  Extends workout model by fill  all missing data with map data
     * @param  AbstractWorkoutFormModel  $workoutModel Model with workout data get from form
     * @param  User $user User object whose is owner of workout
     * @param  Array $data Rest needed data in array (e.g encoded image in base64, distanceTotal, route 
     * data )
     * @return AbstractWorkoutFormModel|null Return filled up workout model or null if cannot complete 
     * all workout data
     */
    public function fillWorkoutModelWithMap(AbstractWorkoutFormModel $workoutModel, User $user, Array $data): ?AbstractWorkoutFormModel;

}
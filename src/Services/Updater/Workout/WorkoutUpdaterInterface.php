<?php 

namespace App\Services\Updater\Workout;

use App\Entity\Workout;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

/** 
 *  Interface for updating Workout entities
 */
interface WorkoutUpdaterInterface
{
    /**
     * update Update entity class with data from model class
     * @param AbstractWorkoutFormModel $dataModel Model data class which will used to update entity
     * @param Workout $workout    Workout class which will be updated
     * @return Workout
     */
     public function update(AbstractWorkoutFormModel $dataModel, Workout $workout): Workout;
}

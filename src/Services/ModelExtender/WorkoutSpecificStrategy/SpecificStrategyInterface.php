<?php 
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutSpecificStrategy;

use App\Form\Model\Workout\AbstractWorkoutFormModel;

interface SpecificStrategyInterface
{

    /**
     * [fill Fill WorkoutModel with required data]
     * @param  AbstractWorkoutFormModel $workoutModel Workout model before fill extended values
     * @return AbstractWorkoutFormModel||null                             
     */
    public function fill(AbstractWorkoutFormModel $workoutModel): ?AbstractWorkoutFormModel;

}

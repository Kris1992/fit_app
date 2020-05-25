<?php 
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutAverageStrategy;

use App\Form\Model\Workout\AbstractWorkoutFormModel;

interface AverageStrategyInterface
{

    /**
     * [fill Fill WorkoutModel with required data]
     * @param  AbstractWorkoutFormModel $workoutModel Workout model before fill extended values
     * @return AbstractWorkoutFormModel                                 
     */
    public function fill(AbstractWorkoutFormModel $workoutModel): AbstractWorkoutFormModel;

}

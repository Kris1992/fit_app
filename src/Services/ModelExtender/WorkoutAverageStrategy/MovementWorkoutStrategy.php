<?php 
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutAverageStrategy;

use App\Form\Model\Workout\AbstractWorkoutFormModel;

class MovementWorkoutStrategy implements AverageStrategyInterface
{

    public function fill(AbstractWorkoutFormModel $workoutModel): AbstractWorkoutFormModel
    {

        $workoutModel
            ->calculateSaveBurnoutEnergyTotal()
            ->calculateSaveDistanceTotal()
            ;

        return $workoutModel;

    }

}

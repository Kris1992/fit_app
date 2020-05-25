<?php 
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutAverageStrategy;

use App\Form\Model\Workout\AbstractWorkoutFormModel;

class WeightWorkoutStrategy implements AverageStrategyInterface
{

    public function fill(AbstractWorkoutFormModel $workoutModel): AbstractWorkoutFormModel
    {
        
        $workoutModel
            ->calculateSaveBurnoutEnergyTotal()
            ->calculateSaveDumbbellWeight()
            ->calculateSaveRepetitionsTotal()
            ;

        return $workoutModel;

    }

}

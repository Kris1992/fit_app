<?php 
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutAverageStrategy;

use App\Form\Model\Workout\AbstractWorkoutFormModel;

class MovementSetWorkoutStrategy implements AverageStrategyInterface
{

    public function fill(AbstractWorkoutFormModel $workoutModel): AbstractWorkoutFormModel
    {
        
        $durationSecondsTotal = 0;
        $burnoutEnergyTotal = 0;
        $distanceTotal = 0;

        $movementSetCollection = $workoutModel->getMovementSets();
        foreach ($movementSetCollection as $movementSet) {
            $movementSet
                ->calculateSaveBurnoutEnergy()
                ->calculateSaveDistance()
                ;
            $durationSecondsTotal += $movementSet->getDurationSeconds();
            $burnoutEnergyTotal += $movementSet->getBurnoutEnergy();
            $distanceTotal += $movementSet->getDistance();
        }
        
        $workoutModel
            ->setDurationSecondsTotal($durationSecondsTotal)
            ->setBurnoutEnergyTotal($burnoutEnergyTotal)
            ->setDistanceTotal($distanceTotal)
            ;
            
        return $workoutModel;

    }

}

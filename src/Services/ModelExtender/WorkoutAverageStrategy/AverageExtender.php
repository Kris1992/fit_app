<?php 
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutAverageStrategy;

use App\Form\Model\Workout\AbstractWorkoutFormModel;

class AverageExtender
{
    private $workoutModel;

    private $averageStrategy;
    
    /**
     * [__construct Create Attack]
     * @param float    $power        Initial value of power 
     * @param StrategyInterface $attackStrategy Choosen strategy
     */
    public function __construct(AbstractWorkoutFormModel $workoutModel, AverageStrategyInterface $averageStrategy)
    {
        $this->workoutModel = $workoutModel;
        $this->averageStrategy = $averageStrategy;
    }

    /**
     * [getFilledWorkoutModel Get filled workout model]
     * @return AbstractWorkoutFormModel
     */
    public function getFilledWorkoutModel(): AbstractWorkoutFormModel
    {   
        return  $this->averageStrategy->fill($this->workoutModel);
    }

}

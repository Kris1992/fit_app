<?php 
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutSpecificStrategy;

use App\Form\Model\Workout\AbstractWorkoutFormModel;

class SpecificExtender
{
    private $workoutModel;

    private $specificStrategy;
    
    /**
     * [__construct SpecificExtender]
     * @param AbstractWorkoutFormModel    $workoutModel Workout model before fill extended data
     * @param AverageStrategyInterface $specificStrategy Choosen strategy
     */
    public function __construct(AbstractWorkoutFormModel $workoutModel, SpecificStrategyInterface $specificStrategy)
    {
        $this->workoutModel = $workoutModel;
        $this->specificStrategy = $specificStrategy;
    }

    /**
     * [getFilledWorkoutModel Get filled workout model]
     * @return AbstractWorkoutFormModel||null
     */
    public function getFilledWorkoutModel(): ?AbstractWorkoutFormModel
    {   
        return $this->specificStrategy->fill($this->workoutModel);
    }

}

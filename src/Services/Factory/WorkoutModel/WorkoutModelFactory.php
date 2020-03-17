<?php

namespace App\Services\Factory\WorkoutModel;

/**
 *  Manage ConcreteFactory
 */
class WorkoutModelFactory
{   

    const MOVEMENT_FACTORY="Movement";
    const MOVEMENTSET_FACTORY="MovementSet";
    const WEIGHT_FACTORY="Weight";
 
    public static function chooseFactory($factoryName, $modelType) {
        switch ($modelType) {
            case 'Average':
                switch($factoryName) {
                    case self::MOVEMENT_FACTORY:
                        return new MovementWorkoutModelAverageFactory();
                    case self::MOVEMENTSET_FACTORY:
                        return new MovementSetWorkoutModelAverageFactory();
                    case self::WEIGHT_FACTORY:
                        return new WeightWorkoutModelAverageFactory();
                    default:
                        return null;
                }
            case 'Specific':
                switch($factoryName) {
                    case self::MOVEMENT_FACTORY:
                        return new MovementWorkoutModelSpecificFactory();
                    case self::MOVEMENTSET_FACTORY:
                        return new MovementSetWorkoutModelSpecificFactory();
                    //case self::WEIGHT_FACTORY:
                        //return new WeightWorkoutModelAverageFactory();
                    default:
                        return null;
                }
            default:
                return null;
        }
        
    }

}

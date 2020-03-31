<?php

namespace App\Services\Factory\WorkoutModel;

/**
 *  Manage ConcreteFactory
 */
class WorkoutModelFactory
{   

    const MOVEMENT_FACTORY="Movement";
    const MOVEMENTSET_FACTORY="MovementSet";
    const BODYWEIGHT_FACTORY="Bodyweight";
    const WEIGHT_FACTORY="Weight";
 
    public static function chooseFactory($factoryName, $modelType) {
        switch ($modelType) {
            case 'Average':
                switch($factoryName) {
                    case self::MOVEMENT_FACTORY:
                        return new MovementWorkoutModelAverageFactory();
                    case self::MOVEMENTSET_FACTORY:
                        return new MovementSetWorkoutModelAverageFactory();
                    case self::BODYWEIGHT_FACTORY:
                        return new BodyweightWorkoutModelAverageFactory();
                    case self::WEIGHT_FACTORY:
                        return new WeightWorkoutModelAverageFactory();
                    default:
                        throw new \Exception("This type of activity is not supported yet");
                }
            case 'Specific':
                switch($factoryName) {
                    case self::MOVEMENT_FACTORY:
                        return new MovementWorkoutModelSpecificFactory();
                    case self::MOVEMENTSET_FACTORY:
                        return new MovementSetWorkoutModelSpecificFactory();
                    case self::BODYWEIGHT_FACTORY:
                        return new BodyweightWorkoutModelSpecificFactory();
                    //case self::WEIGHT_FACTORY:
                        //return new WeightWorkoutModelAverageFactory();
                    default:
                        throw new \Exception("This type of activity is not supported yet");
                }
            default:
                throw new \Error("Unknown workout model factory type");
        }
        
    }

}

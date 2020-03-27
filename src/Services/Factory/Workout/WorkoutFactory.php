<?php

namespace App\Services\Factory\Workout;

/**
 *  Manage ConcreteFactory
 */
class WorkoutFactory
{   

    const MOVEMENT_FACTORY="Movement";
    const MOVEMENTSET_FACTORY="MovementSet";
    const WEIGHT_FACTORY="Weight";
 
    public static function chooseFactory($factoryName) {
        switch($factoryName) {
            case self::MOVEMENT_FACTORY:
                return new MovementWorkoutFactory();
            case self::MOVEMENTSET_FACTORY:
                return new MovementSetWorkoutFactory();
            case self::WEIGHT_FACTORY:
                return new WeightWorkoutFactory();
            default:
                throw new \Exception("This type of activity is not supported yet");
        }
    }
}
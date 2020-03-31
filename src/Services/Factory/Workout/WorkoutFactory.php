<?php

namespace App\Services\Factory\Workout;

/**
 *  Manage ConcreteFactory
 */
class WorkoutFactory
{   

    const MOVEMENT_FACTORY="Movement";
    const MOVEMENTSET_FACTORY="MovementSet";
    const BODYWEIGHT_FACTORY="Bodyweight";
    const WEIGHT_FACTORY="Weight";
 
    public static function chooseFactory($factoryName) {
        switch($factoryName) {
            case self::MOVEMENT_FACTORY:
                return new MovementWorkoutFactory();
            case self::MOVEMENTSET_FACTORY:
                return new MovementSetWorkoutFactory();
            case self::BODYWEIGHT_FACTORY:
                return new BodyweightWorkoutFactory();
            case self::WEIGHT_FACTORY:
                return new WeightWorkoutFactory();
            default:
                throw new \Exception("Unsupported type of activity");
        }
    }
}
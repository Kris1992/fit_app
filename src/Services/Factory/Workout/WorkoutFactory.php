<?php

namespace App\Services\Factory\Workout;

/**
 *  Manage ConcreteFactory
 */
class WorkoutFactory
{   

    const MOVEMENT_FACTORY="Movement";
    const WEIGHT_FACTORY="Weight";
 
    public static function chooseFactory($factoryName) {
        switch($factoryName) {
            case self::MOVEMENT_FACTORY:
                return new MovementWorkoutFactory();
            case self::WEIGHT_FACTORY:
                return new WeightWorkoutFactory();
        }
    }
}
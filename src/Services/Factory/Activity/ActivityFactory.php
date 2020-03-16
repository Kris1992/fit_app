<?php

namespace App\Services\Factory\Activity;

/**
 *  Manage ConcreteFactory
 */
class ActivityFactory
{   

    const MOVEMENT_FACTORY="Movement";
    const MOVEMENTSET_FACTORY="MovementSet";
    const WEIGHT_FACTORY="Weight";
 
    public static function chooseFactory($factoryName) {
        switch($factoryName) {
            case self::MOVEMENT_FACTORY:
                return new MovementActivityFactory();
            case self::MOVEMENTSET_FACTORY:
                return new MovementSetActivityFactory();
            case self::WEIGHT_FACTORY:
                return new WeightActivityFactory();
        }
    }
}
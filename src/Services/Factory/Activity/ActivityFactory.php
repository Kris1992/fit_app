<?php

namespace App\Services\Factory\Activity;

/**
 *  Manage ConcreteFactory
 */
class ActivityFactory
{   

    const MOVEMENT_FACTORY="Movement";
    const MOVEMENTSET_FACTORY="MovementSet";
    const BODYWEIGHT_FACTORY="Bodyweight";
    const WEIGHT_FACTORY="Weight";
 
    public static function chooseFactory($factoryName) {
        switch($factoryName) {
            case self::MOVEMENT_FACTORY:
                return new MovementActivityFactory();
            case self::MOVEMENTSET_FACTORY:
                return new MovementSetActivityFactory();
            case self::BODYWEIGHT_FACTORY:
                return new BodyweightActivityFactory();
            case self::WEIGHT_FACTORY:
                return new WeightActivityFactory();
            default:
                throw new \Exception("This type of activity is not supported yet");
        }
    }
}
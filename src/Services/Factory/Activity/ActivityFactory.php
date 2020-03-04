<?php

namespace App\Services\Factory\Activity;

/**
 *  Manage ConcreteFactory
 */
class ActivityFactory
{   

    const MOVEMENT_FACTORY="Movement";
    const WEIGHT_FACTORY="Weight";
 
    public static function chooseFactory($factoryName) {
        switch($factoryName) {
            case self::MOVEMENT_FACTORY:
                return new MovementActivityFactory();
                //break;
            case self::WEIGHT_FACTORY:
                return new WeightActivityFactory();
                //break;
        }
    }
}
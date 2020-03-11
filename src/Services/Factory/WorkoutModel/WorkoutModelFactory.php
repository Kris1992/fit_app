<?php

namespace App\Services\Factory\WorkoutModel;

/**
 *  Manage ConcreteFactory
 */
class WorkoutModelFactory
{   

    const MOVEMENT_FACTORY="Movement";
    const WEIGHT_FACTORY="Weight";
 
    public static function chooseFactory($factoryName, $modelType) {
        switch ($modelType) {
            case 'Average':
            //Na razie to tak zostawiam ale chyba tutaj nie będzie potrzeby rozdzielania tego na rożne opcje choć zobaczymy jak dodamy więcej typów aktywności
                switch($factoryName) {
                    case self::MOVEMENT_FACTORY:
                        return new MovementWorkoutModelAverageFactory();
                    case self::WEIGHT_FACTORY:
                        return new WeightWorkoutModelAverageFactory();
                    default:
                        return null;
                }
            case 'Specific':
                //to implement
            default:
                return null;
        }
        
    }

}

<?php

namespace App\Services\Transformer\Activity;

/**
 *  Manage Concrete transformer
 */
class ActivityTransformer
{   

    const MOVEMENT_TRANSFORMER="Movement";
    const MOVEMENTSET_TRANSFORMER="MovementSet";
    const BODYWEIGHT_TRANSFORMER="Bodyweight";
    
    const WEIGHT_TRANSFORMER="Weight";

 
    public static function chooseTransformer($transformerName) {
        switch($transformerName) {
            case self::MOVEMENT_TRANSFORMER:
                return new MovementActivityTransformer();
            case self::MOVEMENTSET_TRANSFORMER:
                return new MovementSetActivityTransformer();
            case self::BODYWEIGHT_TRANSFORMER:
                return new BodyweightActivityTransformer();
            case self::WEIGHT_TRANSFORMER:
                return new WeightActivityTransformer();
            //default:
                //throw new \Exception("This is not supported type of activity");
        }
    }
}

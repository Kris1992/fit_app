<?php

namespace App\Services\Transformer\Activity;

use App\Entity\AbstractActivity;
use App\Entity\MovementSetActivity;
use App\Form\Model\Activity\MovementSetActivityFormModel;
use App\Form\Model\Activity\AbstractActivityFormModel;
use App\Services\Converter\ArrayConverter;

class MovementSetActivityTransformer implements ActivityTransformerInterface {

    public function transformToModel(AbstractActivity $activity): AbstractActivityFormModel 
    {
        $movementSetActivityModel = new MovementSetActivityFormModel();
        $movementSetActivityModel
            ->setId($activity->getId())
            ->setType($activity->getType())
            ->setName($activity->getName())
            ->setEnergy($activity->getEnergy())
            ;

        return $movementSetActivityModel;
    }

    public function transformArrayToModel(array $activityData): AbstractActivityFormModel
    {
        $movementSetActivityModel = ArrayConverter::toObject(
            $activityData, 
            new MovementSetActivityFormModel()
        );
        
        return $movementSetActivityModel;
    }

    public function transformToActivity(AbstractActivityFormModel $activityModel, $activity = null): AbstractActivity
    {
        //if Activity is not given create new one
        if ($activity === null) {
            $activity = new MovementSetActivity();
            $activity
                ->setType($activityModel->getType())
                ;
        }

        $activity
            ->setName($activityModel->getName())
            ->setEnergy($activityModel->getEnergy())
            ;

        return $activity;
    }
}
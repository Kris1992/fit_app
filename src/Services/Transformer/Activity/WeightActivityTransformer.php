<?php

namespace App\Services\Transformer\Activity;

use App\Entity\AbstractActivity;
use App\Entity\WeightActivity;
use App\Form\Model\Activity\WeightActivityFormModel;
use App\Form\Model\Activity\AbstractActivityFormModel;
use App\Services\Converter\ArrayConverter;

class WeightActivityTransformer implements ActivityTransformerInterface {
    
    public function transformToModel(AbstractActivity $activity): AbstractActivityFormModel
    {
        $weightActivityModel = new WeightActivityFormModel();
        $weightActivityModel
            ->setId($activity->getId())
            ->setType($activity->getType())
            ->setName($activity->getName())
            ->setEnergy($activity->getEnergy())
            ->setRepetitions($activity->getRepetitions())
            ->setWeight($activity->getWeight());

        return $weightActivityModel;
    }

    public function transformArrayToModel(array $activityData): AbstractActivityFormModel
    {
        $weightActivityModel = ArrayConverter::toObject(
            $activityData, 
            new MovementActivityFormModel()
        );
        
        return $weightActivityModel;
    }
    
    public function transformToActivity(AbstractActivityFormModel $activityModel, $activity = null): AbstractActivity
    {
        //if Activity is not given create new one
        if ($activity === null) {
            $activity = new WeightActivity();
            $activity
                ->setType($activityModel->getType())
                ;
        }

        $activity
            ->setName($activityModel->getName())
            ->setEnergy($activityModel->getEnergy())
            ->setRepetitions($activityModel->getRepetitions())
            ->setWeight($activityModel->getWeight());
            ;

        return $activity;
    }
}
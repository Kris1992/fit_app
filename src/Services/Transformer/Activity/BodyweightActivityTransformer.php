<?php

namespace App\Services\Transformer\Activity;

use App\Entity\AbstractActivity;
use App\Entity\BodyweightActivity;
use App\Form\Model\Activity\BodyweightActivityFormModel;
use App\Form\Model\Activity\AbstractActivityFormModel;
use App\Services\Converter\ArrayConverter;

class BodyweightActivityTransformer implements ActivityTransformerInterface {

    public function transformToModel(AbstractActivity $activity): AbstractActivityFormModel 
    {   
        $activityModel = new BodyweightActivityFormModel();
        $activityModel
            ->setId($activity->getId())
            ->setType($activity->getType())
            ->setName($activity->getName())
            ->setEnergy($activity->getEnergy())
            ->setRepetitionsAvgMin($activity->getRepetitionsAvgMin())
            ->setRepetitionsAvgMax($activity->getRepetitionsAvgMax())
            ->setIntensity($activity->getIntensity())
            ;
            
        return $activityModel;
    }

    public function transformArrayToModel(array $activityData): AbstractActivityFormModel
    {
        $activityModel = ArrayConverter::toObject(
            $activityData, 
            new BodyweightActivityFormModel()
        );
        
        return $activityModel;
    }

    public function transformToActivity(AbstractActivityFormModel $activityModel, $activity = null): AbstractActivity
    {
        //if Activity is not given create new one
        if ($activity === null) {
            $activity = new BodyweightActivity();
            $activity
                ->setType($activityModel->getType())
                ;
        }

        $activity
            ->setName($activityModel->getName())
            ->setEnergy($activityModel->getEnergy())
            ->setRepetitionsAvgMin($activityModel->getRepetitionsAvgMin())
            ->setRepetitionsAvgMax($activityModel->getRepetitionsAvgMax())
            ->setIntensity($activityModel->getIntensity())
            ;

        return $activity;
    }
}
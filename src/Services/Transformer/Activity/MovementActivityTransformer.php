<?php
declare(strict_types=1);

namespace App\Services\Transformer\Activity;

use App\Entity\AbstractActivity;
use App\Entity\MovementActivity;
use App\Form\Model\Activity\MovementActivityFormModel;
use App\Form\Model\Activity\AbstractActivityFormModel;
use App\Services\Converter\ArrayConverter;

class MovementActivityTransformer implements ActivityTransformerInterface {

    public function transformToModel(AbstractActivity $activity): AbstractActivityFormModel 
    {
        $movementActivityModel = new MovementActivityFormModel();
        $movementActivityModel
            ->setId($activity->getId())
            ->setType($activity->getType())
            ->setName($activity->getName())
            ->setEnergy($activity->getEnergy())
            ->setSpeedAverageMin($activity->getSpeedAverageMin())
            ->setSpeedAverageMax($activity->getSpeedAverageMax())
            ->setIntensity($activity->getIntensity())
            ;

        return $movementActivityModel;
    }

    public function transformArrayToModel(array $activityData): AbstractActivityFormModel
    {
        $movementActivityModel = ArrayConverter::toObject(
            $activityData, 
            new MovementActivityFormModel()
        );
        
        return $movementActivityModel;
    }

    public function transformToActivity(AbstractActivityFormModel $activityModel, $activity = null): AbstractActivity
    {
        //if Activity is not given create new one
        if ($activity === null) {
            $activity = new MovementActivity();
            $activity
                ->setType($activityModel->getType())
                ;
        }

        $activity
            ->setName($activityModel->getName())
            ->setEnergy($activityModel->getEnergy())
            ->setSpeedAverageMin($activityModel->getSpeedAverageMin())
            ->setSpeedAverageMax($activityModel->getSpeedAverageMax())
            ->setIntensity($activityModel->getIntensity())
            ;

        return $activity;
    }
}
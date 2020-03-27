<?php

namespace App\Services\Factory\Activity;

use App\Entity\BodyweightActivity;
use App\Entity\AbstractActivity;
use App\Form\Model\Activity\AbstractActivityFormModel;

class BodyweightActivityFactory implements ActivityAbstractFactory {
    
    public function create(AbstractActivityFormModel $activityModel): AbstractActivity
    {
        $bodyweightActivity = new BodyweightActivity();
        $bodyweightActivity
            ->setType($activityModel->getType())
            ->setName($activityModel->getName())
            ->setEnergy($activityModel->getEnergy())
            ->setRepetitionsAvgMin($activityModel->getRepetitionsAvgMin())
            ->setRepetitionsAvgMax($activityModel->getRepetitionsAvgMax())
            ->setIntensity($activityModel->getIntensity())
            ;

        return $bodyweightActivity;
    }
}
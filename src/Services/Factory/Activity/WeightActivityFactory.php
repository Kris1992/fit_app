<?php

namespace App\Services\Factory\Activity;

use App\Entity\WeightActivity;
use App\Entity\AbstractActivity;
use App\Form\Model\Activity\AbstractActivityFormModel;

class WeightActivityFactory implements ActivityAbstractFactory {
    
    public function create(AbstractActivityFormModel $activityModel): AbstractActivity
    {
        $weightActivity = new WeightActivity();
        $weightActivity
            ->setType($activityModel->getType())
            ->setName($activityModel->getName())
            ->setEnergy($activityModel->getEnergy())
            ->setRepetitions($activityModel->getRepetitions())
            ->setWeight($activityModel->getWeight())
            ;

        return $weightActivity;
    }
}
<?php
declare(strict_types=1);

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
            ->setRepetitionsAvgMin($activityModel->getRepetitionsAvgMin())
            ->setRepetitionsAvgMax($activityModel->getRepetitionsAvgMax())
            ->setWeightAvgMin($activityModel->getWeightAvgMin())
            ->setWeightAvgMax($activityModel->getWeightAvgMax())
            ;

        return $weightActivity;
    }
}
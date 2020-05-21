<?php
declare(strict_types=1);

namespace App\Services\Factory\Activity;

use App\Entity\MovementActivity;
use App\Entity\AbstractActivity;
use App\Form\Model\Activity\AbstractActivityFormModel;

class MovementActivityFactory implements ActivityAbstractFactory {
    
    public function create(AbstractActivityFormModel $activityModel): AbstractActivity
    {
        $movementActivity = new MovementActivity();
        $movementActivity
            ->setType($activityModel->getType())
            ->setName($activityModel->getName())
            ->setEnergy($activityModel->getEnergy())
            ->setSpeedAverageMin($activityModel->getSpeedAverageMin())
            ->setSpeedAverageMax($activityModel->getSpeedAverageMax())
            ->setIntensity($activityModel->getIntensity())
            ;

        return $movementActivity;
    }
}
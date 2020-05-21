<?php
declare(strict_types=1);

namespace App\Services\Factory\Activity;

use App\Entity\MovementSetActivity;
use App\Entity\AbstractActivity;
use App\Form\Model\Activity\AbstractActivityFormModel;

class MovementSetActivityFactory implements ActivityAbstractFactory {

    public function create(AbstractActivityFormModel $activityModel): AbstractActivity
    {
        $movementSetActivity = new MovementSetActivity();
        $movementSetActivity
            ->setType($activityModel->getType())
            ->setName($activityModel->getName())
            ->setEnergy($activityModel->getEnergy())
            ;

        return $movementSetActivity;
    }
}
<?php

namespace App\Services\Factory\Activity;

use App\Entity\MovementActivity;
use App\Entity\AbstractActivity;

class MovementActivityFactory implements ActivityAbstractFactory {

    public function createActivity(array $activityArray): AbstractActivity 
    {
        $movementActivity = new MovementActivity();
        $movementActivity->setType($activityArray['type']);
        $movementActivity->setName($activityArray['name']);
        $movementActivity->setEnergy($activityArray['energy']);
        $movementActivity->setSpeedAverageMin($activityArray['speedAverageMin']);
        $movementActivity->setSpeedAverageMax($activityArray['speedAverageMax']);
        $movementActivity->setIntensity($activityArray['intensity']);

        return $movementActivity;
    }
}
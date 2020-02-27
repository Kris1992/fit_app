<?php

namespace App\Services\Factory;

use App\Entity\WeightActivity;
use App\Entity\AbstractActivity;

class WeightActivityFactory implements ActivityAbstractFactory {
    
    public function createActivity(array $activityArray): AbstractActivity
    {
        $weightActivity = new WeightActivity();
        $weightActivity->setType($activityArray['type']);
        $weightActivity->setName($activityArray['name']);
        $weightActivity->setEnergy($activityArray['energy']);
        $weightActivity->setRepetitions($activityArray['repetitions']);
        $weightActivity->setWeight($activityArray['weight']);

        return $weightActivity;
    }
}
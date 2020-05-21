<?php

namespace App\Services\Updater\Challenge;

use App\Entity\Challenge;
use App\Form\Model\Challenge\ChallengeFormModel;

class ChallengeUpdater implements ChallengeUpdaterInterface 
{   
    //Tests
    public function update(ChallengeFormModel $challengeModel, Challenge $challenge): Challenge
    {
        $challenge
            ->setTitle($challengeModel->getTitle())
            ->setActivityName($challengeModel->getActivityName())
            ->setActivityType($challengeModel->getActivityType())
            ->setGoalProperty($challengeModel->getGoalProperty())
            ->setStartAt($challengeModel->getStartAt())
            ->setStopAt($challengeModel->getStopAt())
            ;
        
        return $challenge;
    }
}

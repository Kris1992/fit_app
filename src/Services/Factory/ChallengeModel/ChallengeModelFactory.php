<?php

namespace App\Services\Factory\ChallengeModel;

use App\Entity\Challenge;
use App\Form\Model\Challenge\ChallengeFormModel;

class ChallengeModelFactory implements ChallengeModelFactoryInterface 
{
    //Tests
    public function create(Challenge $challenge): ChallengeFormModel
    {
        $challengeModel = new ChallengeFormModel();
        $challengeModel
            ->setId($challenge->getId())
            ->setTitle($challenge->getTitle())
            ->setActivityName($challenge->getActivityName())
            ->setActivityType($challenge->getActivityType())
            ->setGoalProperty($challenge->getGoalProperty())
            ->setStartAt($challenge->getStartAt())
            ->setStopAt($challenge->getStopAt())
            ;

        return $challengeModel;
    }
}


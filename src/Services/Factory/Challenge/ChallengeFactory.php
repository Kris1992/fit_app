<?php
declare(strict_types=1);

namespace App\Services\Factory\Challenge;

use App\Entity\Challenge;
use App\Form\Model\Challenge\ChallengeFormModel;

class ChallengeFactory implements ChallengeFactoryInterface 
{
    public function create(ChallengeFormModel $challengeModel): Challenge
    { 
        $challenge = new Challenge();
        $challenge
            ->setTitle($challengeModel->getTitle())
            ->setActivityName($challengeModel->getActivityName())
            ->setActivityType($challengeModel->getActivityType())
            ->setGoalProperty($challengeModel->getGoalProperty())
            ->creationTimeStamp()
            ->setStartAt($challengeModel->getStartAt())
            ->setStopAt($challengeModel->getStopAt())
            ;

        return $challenge;
    }
}

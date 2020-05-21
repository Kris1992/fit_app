<?php
declare(strict_types=1);

namespace App\Services\Factory\Challenge;

use App\Entity\Challenge;
use App\Form\Model\Challenge\ChallengeFormModel;

/**
 *  Manage creating of users
 */
interface ChallengeFactoryInterface
{   

    /**
     * create Create challenge 
     * @param ChallengeFormModel $challengeModel Model with challenge data get from form
     * @return Challenge
     */
    public function create(ChallengeFormModel $challengeModel): Challenge;

}

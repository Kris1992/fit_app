<?php
declare(strict_types=1);

namespace App\Services\Factory\ChallengeModel;

use App\Entity\Challenge;
use App\Form\Model\Challenge\ChallengeFormModel;

/**
 *  Manage creating of challenge models
 */
interface ChallengeModelFactoryInterface
{   

    /**
     * create Create challenge model from challenge
     * @param Challenge $challenge Challenge object
     * @return ChallengeFormModel
     */
    public function create(Challenge $challenge): ChallengeFormModel;
}

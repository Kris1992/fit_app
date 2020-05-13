<?php

namespace App\Services\Factory\Reaction;

use App\Entity\Reaction;
use App\Form\Model\Reaction\ReactionFormModel;

/**
 *  Manage creating of reactions
 */
interface ReactionFactoryInterface
{   

    /**
     * create Create reaction
     * @param ReactionFormModel $reactionModel Model with reaction data
     * @return Reaction
     */
    public function create(ReactionFormModel $reactionModel): Reaction;

}

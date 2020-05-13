<?php

namespace App\Services\Factory\Reaction;

use App\Entity\Reaction;
use App\Form\Model\Reaction\ReactionFormModel;

class ReactionFactory implements ReactionFactoryInterface {

    public function create(ReactionFormModel $reactionModel): Reaction
    {
        
        $reaction = new Reaction();
        $reaction
            ->setOwner($reactionModel->getOwner())
            ->setWorkout($reactionModel->getWorkout())
            ->setType($reactionModel->getType())
            ;

        return $reaction;
    }
}

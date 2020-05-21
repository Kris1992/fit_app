<?php
declare(strict_types=1);

namespace App\Services\Factory\ReactionModel;

use App\Entity\User;
use App\Entity\Workout;
use App\Form\Model\Reaction\ReactionFormModel;

class ReactionModelFactory implements ReactionModelFactoryInterface 
{
    
    public function create(User $user, Workout $workout, int $type): ReactionFormModel
    {
        $reactionModel = new ReactionFormModel();
        $reactionModel
            ->setOwner($user)
            ->setWorkout($workout)
            ->setType($type)
            ;

        return $reactionModel;
    }
}

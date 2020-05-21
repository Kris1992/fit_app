<?php
declare(strict_types=1);

namespace App\Services\Factory\ReactionModel;

use App\Entity\User;
use App\Entity\Workout;
use App\Form\Model\Reaction\ReactionFormModel;

/**
 *  Manage creating of reaction models
 */
interface ReactionModelFactoryInterface
{   

    /**
     * create Create reaction model
     * @param User $user User whose does reaction
     * @param Workout $workout Workout which being e.g liked  
     * @param int $type Type of reaction e.g like, love...  
     * @return ReactionFormModel
     */
    public function create(User $user, Workout $workout, int $type): ReactionFormModel;
}

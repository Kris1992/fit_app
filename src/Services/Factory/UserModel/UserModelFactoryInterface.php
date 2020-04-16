<?php

namespace App\Services\Factory\UserModel;

use App\Entity\User;
use App\Form\Model\User\UserRegistrationFormModel;

/**
 *  Manage creating of user models
 */
interface UserModelFactoryInterface
{   

    /**
     * create Create user model from user 
     * @param User $user User object
     * @return UserRegistrationFormModel
     */
    public function create(User $user): UserRegistrationFormModel;
}

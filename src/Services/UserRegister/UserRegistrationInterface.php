<?php

namespace App\Services\UserRegister;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Model\User\UserRegistrationFormModel;

/**
 *  Realize user registration 
 */
interface UserRegistrationInterface
{   
    /**
     * register Realize process registration and return user
     * @param  Request                   $request       
     * @param  UserRegistrationFormModel $userModel     User model from form
     * @param  File                      $uploadedImage File with uploaded image [optional]
     * @return User
     */
    public function register(Request $request, UserRegistrationFormModel $userModel, ?File $uploadedImage): User;
}

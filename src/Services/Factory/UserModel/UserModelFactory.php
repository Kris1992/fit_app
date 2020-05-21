<?php
declare(strict_types=1);

namespace App\Services\Factory\UserModel;

use App\Entity\User;
use App\Form\Model\User\UserRegistrationFormModel;

class UserModelFactory implements UserModelFactoryInterface 
{
    
    public function create(User $user): UserRegistrationFormModel
    {
   
        $userModel = new UserRegistrationFormModel();
        $userModel
            ->setId($user->getId())
            ->setEmail($user->getEmail())
            ->setFirstName($user->getFirstName())
            ->setSecondName($user->getSecondName())
            ->setLogin($user->getLogin())
            ->setGender($user->getGender())
            ->setBirthdate($user->getBirthdate())
            ->setWeight($user->getWeight())
            ->setHeight($user->getHeight())
            ->setRole($user->getRole())
            ->setImageFilename($user->getImageFilename())
            ;

        return $userModel;
    }
}

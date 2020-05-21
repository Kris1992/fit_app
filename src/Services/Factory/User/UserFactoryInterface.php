<?php
declare(strict_types=1);

namespace App\Services\Factory\User;

use App\Entity\User;
use App\Form\Model\User\UserRegistrationFormModel;
use Symfony\Component\HttpFoundation\File\File;

/**
 *  Manage creating of users
 */
interface UserFactoryInterface
{   

    /**
     * create Create user 
     * @param UserRegistrationFormModel $userModel Model with user data get from form
     * @param string $role String with role name [optional]
     * @param File $uploadedImage Uploaded image [optional]
     * @return User
     */
    public function create(UserRegistrationFormModel $userModel,?string $role, ?File $uploadedImage): User;

}

<?php

namespace App\Services\Updater\User;

use App\Entity\User;
use App\Form\Model\User\UserRegistrationFormModel;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\ImagesManager\ImagesManagerInterface;

class UserUpdater implements UserUpdaterInterface 
{

    private $imagesManager;

    /**
     * UserUpdater Constructor
     * 
     * @param ImagesManagerInterface $imagesManager
     */
    public function __construct(ImagesManagerInterface $imagesManager)  
    {
        $this->imagesManager = $imagesManager;
    }

    public function update(UserRegistrationFormModel $userModel, User $user, ?File $uploadedImage): User
    {

        $user
            ->setEmail($userModel->getEmail())
            ->setFirstName($userModel->getFirstName())
            ->setSecondName($userModel->getSecondName())
            ->setGender($userModel->getGender())
            ->setBirthdate($userModel->getBirthdate())
            ->setWeight($userModel->getWeight())
            ->setHeight($userModel->getHeight())
            ->setRoles([$userModel->getRole()])
            ;

            if($uploadedImage) {
                $newFilename = $this->imagesManager->uploadImage($uploadedImage, $user->getImageFilename(), $user->getLogin());
                $user->setImageFilename($newFilename);
            }
        
        return $user;
    }
}
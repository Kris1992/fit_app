<?php

namespace App\Services\Factory\User;

use App\Entity\User;
use App\Form\Model\User\UserRegistrationFormModel;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Services\ImagesManager\ImagesManagerInterface;

class UserFactory implements UserFactoryInterface {

    private $passwordEncoder;
    private $imagesManager;

    /**
     * UserFactory Constructor
     * 
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ImagesManagerInterface $imagesManager
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, ImagesManagerInterface $imagesManager)  
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->imagesManager = $imagesManager;
    }
    
    public function create(UserRegistrationFormModel $userModel,?string $role, ?File $uploadedImage): User
    {
        if (!$role) {
            $role = 'ROLE_USER';
        }
        
        $user = new User();
        $user
            ->setEmail($userModel->getEmail())
            ->setFirstName($userModel->getFirstName())
            ->setSecondName($userModel->getSecondName())
            ->setGender($userModel->getGender())
            ->setRoles([$role])
            ;

        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $userModel->getPlainPassword()
        ));

        if ($uploadedImage) {
            $newFilename = $this->imagesManager->uploadUserImage($uploadedImage, null);
            $user->setImageFilename($newFilename);
        }

        if (true === $userModel->getAgreeTerms()) {
            $user->agreeToTerms();
        } else {
            throw new Exception("You not agree terms?");
        }

        return $user;
    }
}

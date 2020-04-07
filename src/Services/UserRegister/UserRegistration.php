<?php

namespace App\Services\UserRegister;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Model\User\UserRegistrationFormModel;
use ReCaptcha\ReCaptcha;
use App\Services\Factory\User\UserFactoryInterface;

class UserRegistration implements UserRegistrationInterface
{

    private $userFactoryInterface;
    private $secret_key;

    /**
     * UserRegistration Constructor
     * 
     * @param UserFactoryInterface $userFactoryInterface
     * @param string $secret_key
     */
    public function __construct(UserFactoryInterface $userFactoryInterface, string $secret_key) 
    {
        $this->userFactoryInterface = $userFactoryInterface;
        $this->secret_key = $secret_key;
    }

    public function register(Request $request, UserRegistrationFormModel $userModel,?File $uploadedImage): User
    {
        $isHuman = $this->checkCatchpa($request);

        if (!$isHuman->isSuccess()) {
            throw new \Exception('The ReCaptcha was not entered correctly!');
        }
        
        $user = $this->userFactoryInterface->create($userModel, null, $uploadedImage);

        return $user;
    }

    private function checkCatchpa(Request $request)
    {
        //If you run in localhost by symfony serve it will not work because of port
        $recaptcha = new ReCaptcha($this->secret_key);
        return $isHuman = $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])//$_SERVER['REMOTE_ADDR'])
                ->verify($request->get('g-recaptcha-response'), $_SERVER['REMOTE_ADDR']);
    }
}

<?php

namespace spec\App\Services\Factory\UserModel;

use App\Services\Factory\UserModel\UserModelFactory;
use App\Services\Factory\UserModel\UserModelFactoryInterface;
use PhpSpec\ObjectBehavior;
use App\Entity\User;
use App\Form\Model\User\UserRegistrationFormModel;


class UserModelFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UserModelFactory::class);
    }

    function it_impelements_user_model_factory_interface()
    {
        $this->shouldImplement(UserModelFactoryInterface::class);
    }

    function it_should_be_able_to_create_user_model()
    {
        $user = new User();
        $user
            ->setEmail('exampleuser@fit.com')
            ->setFirstName('Adam')
            ->setSecondName('Kowalski')
            ->saveLogin()
            ->setGender('Male')
            ->setRoles(['ROLE_USER'])
            ->setBirthdate(new \DateTime('2000-01-01'))
            ->setWeight(80)
            ->setHeight(150)
            ->setImageFilename('fakename.png')
            ;

        $userModel = $this->create($user);
        $userModel->shouldBeAnInstanceOf(UserRegistrationFormModel::class);
        $userModel->getEmail()->shouldReturn('exampleuser@fit.com');
        $userModel->getFirstName()->shouldReturn('Adam');
        $userModel->getSecondName()->shouldReturn('Kowalski');
        $userModel->getGender()->shouldReturn('Male');
        $userModel->getRole()->shouldReturn('ROLE_USER');
        $userModel->getBirthdate()->shouldReturnAnInstanceOf('\DateTime');
        $userModel->getWeight()->shouldReturn(80);
        $userModel->getHeight()->shouldReturn(150);
        $userModel->getImageFilename()->shouldReturn('fakename.png');
        $userModel->getLogin()->shouldBeString();
    }
}


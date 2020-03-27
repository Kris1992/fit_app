<?php

namespace spec\App\Services\Factory\User;

use App\Services\Factory\User\UserFactory;
use App\Services\Factory\User\UserFactoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Services\ImagesManager\ImagesManagerInterface;
use App\Form\Model\User\UserRegistrationFormModel;
use Symfony\Component\HttpFoundation\File\File;

class UserFactorySpec extends ObjectBehavior
{
    function let(UserPasswordEncoderInterface $passwordEncoder, ImagesManagerInterface $imagesManager)
    {
        $this->beConstructedWith($passwordEncoder, $imagesManager);
    }

    function it_is_initializable()
    {   
        $this->shouldHaveType(UserFactory::class);
    }

    function it_impelements_user_factory_interface()
    {
        $this->shouldImplement(UserFactoryInterface::class);
    }

    function it_should_be_able_to_create_user($passwordEncoder)
    {
        $user = new User();
        $userModel = new UserRegistrationFormModel();
        $userModel
            ->setEmail('exampleuser@fit.com')
            ->setFirstName('Adam')
            ->setSecondName('Kowalski')
            ->setGender('Male')
            ->setPlainPassword('example01')
            ->setAgreeTerms(true)
            ;
        $passwordEncoder->encodePassword(Argument::type(User::class),Argument::type('string'))->willReturn('example01');

        $user = $this->create($userModel, null, null);
        $user->shouldBeAnInstanceOf(User::class);
        $user->getEmail()->shouldReturn('exampleuser@fit.com');
        $user->getFirstName()->shouldReturn('Adam');
        $user->getSecondName()->shouldReturn('Kowalski');
        $user->getGender()->shouldReturn('Male');
        $user->isAdmin()->shouldReturn(false);
    }

    function it_should_be_able_to_create_admin($passwordEncoder)
    {
        $user = new User();
        $userModel = new UserRegistrationFormModel();
        $userModel
            ->setEmail('exampleuser@fit.com')
            ->setFirstName('Adam')
            ->setSecondName('Kowalski')
            ->setGender('Male')
            ->setPlainPassword('example01')
            ->setAgreeTerms(true)
            ;
        $passwordEncoder->encodePassword(Argument::type(User::class),Argument::type('string'))->willReturn('example01');

        $user = $this->create($userModel, 'ROLE_ADMIN', null);
        $user->shouldBeAnInstanceOf(User::class);

        $user->isAdmin()->shouldReturn(true);
    }

    function it_should_not_allow_to_create_user_whose_not_accept_terms($passwordEncoder)
    {
        $user = new User();
        $userModel = new UserRegistrationFormModel();
        $userModel
            ->setEmail('exampleuser@fit.com')
            ->setFirstName('Adam')
            ->setSecondName('Kowalski')
            ->setGender('Male')
            ->setPlainPassword('example01')
            ;
        $passwordEncoder->encodePassword(Argument::type(User::class),Argument::type('string'))->willReturn('example01');

        $this
            ->shouldThrow(\Exception::class)
            ->during('create', [$userModel, null, null]);
    }

    // TO DO (Don't Mock What You Don't Own) Implement wrapper interface
    function it_should_be_able_to_create_user_with_image($passwordEncoder, $imagesManager)
    {   
        //$this->beConstructedThrough('create', [$userModel, null, $file]);
        /*$userModel = new UserRegistrationFormModel();
        $userModel
            ->setEmail('exampleuser@fit.com')
            ->setFirstName('Adam')
            ->setSecondName('Kowalski')
            ->setGender('Male')
            ->setPlainPassword('example01')
            ->setAgreeTerms(true)
            ;
        $passwordEncoder->encodePassword(Argument::type(User::class),Argument::type('string'))->willReturn('example01');

        $user = $this->create($userModel, null, Argument::type(File::class));
        $user->shouldBeAnInstanceOf(User::class);
        $user->isAdmin()->shouldReturn(false);*/
    }
}

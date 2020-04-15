<?php

namespace spec\App\Services\Updater\User;

use App\Services\Updater\User\UserUpdater;
use App\Services\Updater\User\UserUpdaterInterface;
use App\Services\ImagesManager\ImagesManagerInterface;
use App\Form\Model\User\UserRegistrationFormModel;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\User;
use PhpSpec\ObjectBehavior;

class UserUpdaterSpec extends ObjectBehavior
{

    function let(ImagesManagerInterface $imagesManager)
    {
        $this->beConstructedWith($imagesManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserUpdater::class);
    }

    function it_impelements_user_updater_interface()
    {
        $this->shouldImplement(UserUpdaterInterface::class);
    }

    function it_should_be_able_to_update_user()
    {
        $user = new User();
        $user
            ->setEmail('prevuser@fit.com')
            ->setFirstName('Mariola')
            ->setSecondName('Kowalska')
            ->setGender('Female')
            ->setBirthdate(new \DateTime('2019-05-05'))
            ->setWeight(90)
            ->setHeight(160)
            ->setRoles(['ROLE_ADMIN'])
            ;

        $userModel = new UserRegistrationFormModel();
        $userModel
            ->setEmail('exampleuser@fit.com')
            ->setFirstName('Adam')
            ->setSecondName('Kowalski')
            ->setGender('Male')
            ->setBirthdate(new \DateTime('2011-01-01'))
            ->setWeight(80)
            ->setHeight(150)
            ->setRole('ROLE_USER')
            ;

        $user = $this->update($userModel, $user, null);
        $user->shouldBeAnInstanceOf(User::class);
        $user->getEmail()->shouldReturn('exampleuser@fit.com');
        $user->getFirstName()->shouldReturn('Adam');
        $user->getSecondName()->shouldReturn('Kowalski');
        $user->getGender()->shouldReturn('Male');
        $user->getBirthdate()->shouldBeLike(new \DateTime('2011-01-01'));
        $user->getWeight()->shouldReturn(80);
        $user->getHeight()->shouldReturn(150);
        $user->isAdmin()->shouldReturn(false);
    }
}

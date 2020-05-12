<?php

namespace spec\App\Services\Updater\Curiosity;

use App\Services\Updater\Curiosity\CuriosityUpdater;
use PhpSpec\ObjectBehavior;
use App\Entity\Curiosity;
use App\Entity\User;
use App\Form\Model\Curiosity\CuriosityFormModel;
use App\Services\Updater\Curiosity\CuriosityUpdaterInterface;
use App\Services\ImagesManager\ImagesManagerInterface;

class CuriosityUpdaterSpec extends ObjectBehavior
{
    
    function let(ImagesManagerInterface $curiositiesImagesManager)
    {
        $this->beConstructedWith($curiositiesImagesManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CuriosityUpdater::class);
    }

    function it_impelements_curiosity_updater_interface()
    {
        $this->shouldImplement(CuriosityUpdaterInterface::class);
    }

    function it_should_be_able_to_update_unpublished_curiosity()
    {
        $user = new User();
        $curiosity = new Curiosity();
        $curiosity
            ->setAuthor($user)
            ->setTitle('Old Simple Title')
            ->setDescription('Old Simple description')
            ->setContent('Old Content of curiosity')
            ;

        $curiosityModel = new CuriosityFormModel();
        $curiosityModel
            ->setTitle('New Simple Title')
            ->setDescription('New Simple description')
            ->setContent('New Content of curiosity')
            ;

        $curiosity = $this->update($curiosityModel, $curiosity, null);
        $curiosity->shouldBeAnInstanceOf(Curiosity::class);
        $curiosity->getAuthor()->shouldBeAnInstanceOf(User::class);
        $curiosity->getTitle()->shouldReturn('New Simple Title');
        $curiosity->getDescription()->shouldReturn('New Simple description');
        $curiosity->getContent()->shouldReturn('New Content of curiosity');
        $curiosity->getUpdatedAt()->shouldReturnAnInstanceOf('\DateTime');
        $curiosity->isPublished()->shouldReturn(false);
    }

    function it_should_be_able_to_update_unpublished_curiosity_with_publish()
    {
        $user = new User();
        $curiosity = new Curiosity();
        $curiosity
            ->setAuthor($user)
            ->setTitle('Old Simple Title')
            ->setDescription('Old Simple description')
            ->setContent('Old Content of curiosity')
            ;

        $curiosityModel = new CuriosityFormModel();
        $curiosityModel
            ->setTitle('New Simple Title')
            ->setDescription('New Simple description')
            ->setContent('New Content of curiosity')
            ->setIsPublished(true)
            ;

        $curiosity = $this->update($curiosityModel, $curiosity, null);
        $curiosity->shouldBeAnInstanceOf(Curiosity::class);
        $curiosity->getAuthor()->shouldBeAnInstanceOf(User::class);
        $curiosity->getTitle()->shouldReturn('New Simple Title');
        $curiosity->getDescription()->shouldReturn('New Simple description');
        $curiosity->getContent()->shouldReturn('New Content of curiosity');
        $curiosity->getUpdatedAt()->shouldReturnAnInstanceOf('\DateTime');
        $curiosity->isPublished()->shouldReturn(true);
    }
}


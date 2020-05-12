<?php

namespace spec\App\Services\Factory\Curiosity;

use App\Services\Factory\Curiosity\CuriosityFactory;
use PhpSpec\ObjectBehavior;
use App\Entity\Curiosity;
use App\Entity\User;
use App\Form\Model\Curiosity\CuriosityFormModel;
use App\Services\Factory\Curiosity\CuriosityFactoryInterface;
use App\Services\ImagesManager\ImagesManagerInterface;

class CuriosityFactorySpec extends ObjectBehavior
{
    function let(ImagesManagerInterface $curiositiesImagesManager)
    {
        $this->beConstructedWith($curiositiesImagesManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CuriosityFactory::class);
    }

    function it_impelements_curiosity_factory_interface()
    {
        $this->shouldImplement(CuriosityFactoryInterface::class);
    }

    function it_should_be_able_to_create_unpublished_curiosity()
    {
        $user = new User();
        $curiosityModel = new CuriosityFormModel();
        $curiosityModel
            ->setTitle('Simple Title')
            ->setDescription('Simple description')
            ->setContent('Content of curiosity')
            ;

        $curiosity = $this->create($curiosityModel, $user, null);
        $curiosity->shouldBeAnInstanceOf(Curiosity::class);
        $curiosity->getAuthor()->shouldBeAnInstanceOf(User::class);
        $curiosity->getTitle()->shouldReturn('Simple Title');
        $curiosity->getDescription()->shouldReturn('Simple description');
        $curiosity->getContent()->shouldReturn('Content of curiosity');
        $curiosity->isPublished()->shouldReturn(false);
        $curiosity->getCreatedAt()->shouldReturnAnInstanceOf('\DateTime');
        $curiosity->getUpdatedAt()->shouldReturn(null);
    }

    function it_should_be_able_to_create_published_curiosity()
    {
        $user = new User();
        $curiosityModel = new CuriosityFormModel();
        $curiosityModel
            ->setTitle('Simple Title')
            ->setDescription('Simple description')
            ->setContent('Content of curiosity')
            ->setIsPublished(true)
            ;

        $curiosity = $this->create($curiosityModel, $user, null);
        $curiosity->shouldBeAnInstanceOf(Curiosity::class);
        $curiosity->getAuthor()->shouldBeAnInstanceOf(User::class);
        $curiosity->getTitle()->shouldReturn('Simple Title');
        $curiosity->getDescription()->shouldReturn('Simple description');
        $curiosity->getContent()->shouldReturn('Content of curiosity');
        $curiosity->isPublished()->shouldReturn(true);
        $curiosity->getCreatedAt()->shouldReturnAnInstanceOf('\DateTime');
        $curiosity->getUpdatedAt()->shouldReturn(null);
    }

}

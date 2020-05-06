<?php

namespace spec\App\Services\Factory\CuriosityModel;

use App\Services\Factory\CuriosityModel\CuriosityModelFactory;
use PhpSpec\ObjectBehavior;
use App\Entity\Curiosity;
use App\Entity\User;
use App\Form\Model\Curiosity\CuriosityFormModel;
use App\Services\Factory\CuriosityModel\CuriosityModelFactoryInterface;

class CuriosityModelFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CuriosityModelFactory::class);
    }

    function it_impelements_curiosity_model_factory_interface()
    {
        $this->shouldImplement(CuriosityModelFactoryInterface::class);
    }

    function it_should_be_able_to_create_curiosity_model()
    {
        $user = new User();
        $curiosity = new Curiosity();
        $curiosity
            ->setAuthor($user)
            ->setTitle('Simple Title')
            ->setDescription('Simple description')
            ->setContent('Content of curiosity')
            ;

        $curiosityModel = $this->create($curiosity);
        $curiosityModel->shouldBeAnInstanceOf(CuriosityFormModel::class);
        $curiosityModel->getAuthor()->shouldBeAnInstanceOf(User::class);
        $curiosityModel->getTitle()->shouldReturn('Simple Title');
        $curiosityModel->getDescription()->shouldReturn('Simple description');
        $curiosityModel->getContent()->shouldReturn('Content of curiosity');
        $curiosityModel->getIsPublished()->shouldReturn(false);
    }
}

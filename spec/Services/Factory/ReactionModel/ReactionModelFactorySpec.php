<?php

namespace spec\App\Services\Factory\ReactionModel;

use App\Services\Factory\ReactionModel\ReactionModelFactory;
use App\Services\Factory\ReactionModel\ReactionModelFactoryInterface;
use PhpSpec\ObjectBehavior;
use App\Entity\Reaction;
use App\Entity\Workout;
use App\Entity\User;
use App\Form\Model\Reaction\ReactionFormModel;

class ReactionModelFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ReactionModelFactory::class);
    }

    function it_impelements_reaction_model_factory_interface()
    {
        $this->shouldImplement(ReactionModelFactoryInterface::class);
    }

    function it_should_be_able_to_create_reaction_model()
    {
        $user = new User();
        $workout = new Workout();

        $reactionModel = $this->create($user, $workout, 1);
        $reactionModel->shouldBeAnInstanceOf(ReactionFormModel::class);
        $reactionModel->getOwner()->shouldBeAnInstanceOf($user);
        $reactionModel->getWorkout()->shouldBeAnInstanceOf($workout);
        $reactionModel->getType()->shouldReturn(1);
    }
}

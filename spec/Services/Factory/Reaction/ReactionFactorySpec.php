<?php

namespace spec\App\Services\Factory\Reaction;

use App\Services\Factory\Reaction\ReactionFactory;
use App\Services\Factory\Reaction\ReactionFactoryInterface;
use PhpSpec\ObjectBehavior;
use App\Entity\Reaction;
use App\Entity\Workout;
use App\Entity\User;
use App\Form\Model\Reaction\ReactionFormModel;

class ReactionFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ReactionFactory::class);
    }

    function it_impelements_reaction_factory_interface()
    {
        $this->shouldImplement(ReactionFactoryInterface::class);
    }

    function it_should_be_able_to_create_reaction()
    {
        $user = new User();
        $workout = new Workout();
        $reactionModel = new ReactionFormModel();
        $reactionModel
            ->setOwner($user)
            ->setWorkout($workout)
            ->setType(1)
            ;

        $reaction = $this->create($reactionModel);
        $reaction->shouldBeAnInstanceOf(Reaction::class);
        $reaction->getOwner()->shouldBeAnInstanceOf($user);
        $reaction->getWorkout()->shouldBeAnInstanceOf($workout);
        $reaction->getType()->shouldReturn(1);
    }
}


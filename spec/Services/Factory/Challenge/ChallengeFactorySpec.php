<?php

namespace spec\App\Services\Factory\Challenge;

use App\Services\Factory\Challenge\ChallengeFactory;
use App\Services\Factory\Challenge\ChallengeFactoryInterface;
use PhpSpec\ObjectBehavior;

use App\Entity\Challenge;
use App\Form\Model\Challenge\ChallengeFormModel;

class ChallengeFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ChallengeFactory::class);
    }
    
    function it_impelements_challenge_factory_interface()
    {
        $this->shouldImplement(ChallengeFactoryInterface::class);
    }

    function it_should_be_able_to_create_challenge()
    {
        $challengeModel = new ChallengeFormModel();

        $challengeModel
            ->setTitle('Simple Title')
            ->setActivityName('Running')
            ->setActivityType('Movement')
            ->setGoalProperty('durationSecondsTotal')
            ->setStartAt(new \DateTime())
            ->setStopAt(new \DateTime())
            ;

        $challenge = $this->create($challengeModel);
        $challenge->shouldBeAnInstanceOf(Challenge::class);
        $challenge->getTitle()->shouldReturn('Simple Title');
        $challenge->getActivityName()->shouldReturn('Running');
        $challenge->getActivityType()->shouldReturn('Movement');
        $challenge->getGoalProperty()->shouldReturn('durationSecondsTotal');
        $challenge->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
        $challenge->getStopAt()->shouldReturnAnInstanceOf('\DateTime');
        $challenge->getCreatedAt()->shouldReturnAnInstanceOf('\DateTime');
    }
}

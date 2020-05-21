<?php

namespace spec\App\Services\Factory\ChallengeModel;

use App\Services\Factory\ChallengeModel\ChallengeModelFactoryInterface;
use App\Services\Factory\ChallengeModel\ChallengeModelFactory;
use PhpSpec\ObjectBehavior;
use App\Entity\Challenge;
use App\Form\Model\Challenge\ChallengeFormModel;

class ChallengeModelFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ChallengeModelFactory::class);
    }

    function it_impelements_challenge_model_factory_interface()
    {
        $this->shouldImplement(ChallengeModelFactoryInterface::class);
    }

    function it_should_be_able_to_create_challenge_model()
    {
        $challenge = new Challenge();
        $challenge
            ->setTitle('Simple Title')
            ->setActivityName('Running')
            ->setActivityType('Movement')
            ->setGoalProperty('durationSecondsTotal')
            ->creationTimeStamp()
            ->setStartAt(new \DateTime())
            ->setStopAt(new \DateTime())
            ;

        $challengeModel = $this->create($challenge);
        $challengeModel->shouldBeAnInstanceOf(ChallengeFormModel::class);
        $challengeModel->getTitle()->shouldReturn('Simple Title');
        $challengeModel->getActivityName()->shouldReturn('Running');
        $challengeModel->getActivityType()->shouldReturn('Movement');
        $challengeModel->getGoalProperty()->shouldReturn('durationSecondsTotal');
        $challengeModel->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
        $challengeModel->getStopAt()->shouldReturnAnInstanceOf('\DateTime');
    }
}

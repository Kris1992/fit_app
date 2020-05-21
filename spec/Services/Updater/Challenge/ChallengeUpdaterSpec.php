<?php

namespace spec\App\Services\Updater\Challenge;

use App\Services\Updater\Challenge\ChallengeUpdaterInterface;
use App\Services\Updater\Challenge\ChallengeUpdater;
use PhpSpec\ObjectBehavior;
use App\Entity\Challenge;
use App\Form\Model\Challenge\ChallengeFormModel;

class ChallengeUpdaterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ChallengeUpdater::class);
    }

    function it_impelements_challenge_updater_interface()
    {
        $this->shouldImplement(ChallengeUpdaterInterface::class);
    }

    function it_should_be_able_to_update_challenge()
    {
        $challengeModel = new ChallengeFormModel();
        $challengeModel
            ->setTitle('Simple Title Model')
            ->setActivityName('Running')
            ->setActivityType('Movement')
            ->setGoalProperty('durationSecondsTotal')
            ->setStartAt(new \DateTime())
            ->setStopAt(new \DateTime())
            ;

        $challenge = new Challenge();
        $challenge
            ->setTitle('Title')
            ->setActivityName('Push-ups')
            ->setActivityType('Bodyweight')
            ->setGoalProperty('repetitionsTotal')
            ->creationTimeStamp()
            ->setStartAt(new \DateTime())
            ->setStopAt(new \DateTime())
            ;

        $challenge = $this->update($challengeModel, $challenge);
        $challenge->shouldBeAnInstanceOf(Challenge::class);
        $challenge->getTitle()->shouldReturn('Simple Title Model');
        $challenge->getActivityName()->shouldReturn('Running');
        $challenge->getActivityType()->shouldReturn('Movement');
        $challenge->getGoalProperty()->shouldReturn('durationSecondsTotal');
        $challenge->getCreatedAt()->shouldReturnAnInstanceOf('\DateTime');
    }
}

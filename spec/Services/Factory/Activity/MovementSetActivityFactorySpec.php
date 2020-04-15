<?php

namespace spec\App\Services\Factory\Activity;

use App\Services\Factory\Activity\MovementSetActivityFactory;
use App\Services\Factory\Activity\ActivityAbstractFactory;
use App\Form\Model\Activity\MovementSetActivityFormModel;
use App\Entity\MovementSetActivity;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MovementSetActivityFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MovementSetActivityFactory::class);
    }

    function it_impelements_activity_abstract_factory_interface()
    {
        $this->shouldImplement(ActivityAbstractFactory::class);
    }

    function it_should_be_able_to_create_movement_set_activity()
    {
        $activityModel = new MovementSetActivityFormModel();
        $activityModel
            ->setType('MovementSet')
            ->setName('Running Circuit')
            ;

        $activity = $this->create($activityModel);
        $activity->shouldBeAnInstanceOf(MovementSetActivity::class);
        $activity->getType()->shouldBe('MovementSet');
        $activity->getName()->shouldReturn('Running Circuit');
        $activity->getEnergy()->shouldReturn(1);
    }
}
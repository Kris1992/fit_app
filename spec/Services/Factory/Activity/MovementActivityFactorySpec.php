<?php

namespace spec\App\Services\Factory\Activity;

use App\Services\Factory\Activity\MovementActivityFactory;
use App\Services\Factory\Activity\ActivityAbstractFactory;
use App\Form\Model\Activity\MovementActivityFormModel;
use App\Entity\MovementActivity;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MovementActivityFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MovementActivityFactory::class);
    }

    function it_impelements_activity_abstract_factory_interface()
    {
        $this->shouldImplement(ActivityAbstractFactory::class);
    }

    function it_should_be_able_to_create_movement_activity()
    {
        $activityModel = new MovementActivityFormModel();
        $activityModel
            ->setType('Movement')
            ->setName('Running')
            ->setEnergy(500)
            ->setSpeedAverageMin(10.0)
            ->setSpeedAverageMax(15.0)
            ->setIntensity('Normal')
            ;

        $activity = $this->create($activityModel);
        $activity->shouldBeAnInstanceOf(MovementActivity::class);
        $activity->getType()->shouldBe('Movement');
        $activity->getName()->shouldReturn('Running');
        $activity->getEnergy()->shouldReturn(500);
        $activity->getSpeedAverageMin()->shouldReturn(10.0);
        $activity->getSpeedAverageMax()->shouldReturn(15.0);
        $activity->getIntensity()->shouldReturn('Normal');
    }
}
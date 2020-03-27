<?php

namespace spec\App\Services\Factory\Activity;

use App\Services\Factory\Activity\BodyweightActivityFactory;
use App\Services\Factory\Activity\ActivityAbstractFactory;
use App\Form\Model\Activity\BodyweightActivityFormModel;
use App\Entity\BodyweightActivity;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BodyweightActivityFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BodyweightActivityFactory::class);
    }

    function it_impelements_activity_abstract_factory_interface()
    {
        $this->shouldImplement(ActivityAbstractFactory::class);
    }

    function it_should_be_able_to_create_bodyweight_activity()
    {
        $activityModel = new BodyweightActivityFormModel();
        $activityModel
            ->setType('Bodyweight')
            ->setName('Pump')
            ->setEnergy(500)
            ->setRepetitionsAvgMin(40)
            ->setRepetitionsAvgMax(60)
            ->setIntensity('Low')
            ;

        $activity = $this->create($activityModel);
        $activity->shouldBeAnInstanceOf(BodyweightActivity::class);
        $activity->getType()->shouldBe('Bodyweight');
        $activity->getName()->shouldReturn('Pump');
        $activity->getEnergy()->shouldReturn(500);
        $activity->getRepetitionsAvgMin()->shouldReturn(40);
        $activity->getRepetitionsAvgMax()->shouldReturn(60);
        $activity->getIntensity()->shouldReturn('Low');
    }

}
<?php

namespace spec\App\Services\Factory\Activity;

use App\Services\Factory\Activity\WeightActivityFactory;
use App\Services\Factory\Activity\ActivityAbstractFactory;
use App\Form\Model\Activity\WeightActivityFormModel;
use App\Entity\WeightActivity;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WeightActivityFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WeightActivityFactory::class);
    }

    function it_impelements_activity_abstract_factory_interface()
    {
        $this->shouldImplement(ActivityAbstractFactory::class);
    }

    function it_should_be_able_to_create_weight_activity()
    {
        $activityModel = new WeightActivityFormModel();
        $activityModel
            ->setType('Weight')
            ->setName('Barbell Bench Press')
            ->setEnergy(300)
            ->setRepetitionsAvgMin(30)
            ->setRepetitionsAvgMax(60)
            ->setWeightAvgMin(30.0)
            ->setWeightAvgMax(50.0)
            ;

        $activity = $this->create($activityModel);
        $activity->shouldBeAnInstanceOf(WeightActivity::class);
        $activity->getType()->shouldReturn('Weight');
        $activity->getName()->shouldReturn('Barbell Bench Press');
        $activity->getEnergy()->shouldReturn(300);
        $activity->getRepetitionsAvgMin()->shouldReturn(30);
        $activity->getRepetitionsAvgMax()->shouldReturn(60);
        $activity->getWeightAvgMin()->shouldReturn(30.0);
        $activity->getWeightAvgMax()->shouldReturn(50.0);
    }

}
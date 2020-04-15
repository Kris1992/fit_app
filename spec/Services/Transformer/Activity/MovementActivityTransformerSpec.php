<?php

namespace spec\App\Services\Transformer\Activity;

use App\Services\Transformer\Activity\MovementActivityTransformer;
use App\Services\Transformer\Activity\ActivityTransformerInterface;
use App\Form\Model\Activity\MovementActivityFormModel;
use App\Entity\MovementActivity;
use PhpSpec\ObjectBehavior;

class MovementActivityTransformerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MovementActivityTransformer::class);
    }

    function it_impelements_activity_transformer_interface()
    {
        $this->shouldImplement(ActivityTransformerInterface::class);
    }

    function it_should_be_able_to_transform_movement_activity_to_model()
    {
        $activity = new MovementActivity();
        $activity
            ->setType('Movement')
            ->setName('Running')
            ->setEnergy(500)
            ->setSpeedAverageMin(10.0)
            ->setSpeedAverageMax(15.0)
            ->setIntensity('Normal')
            ;

        $activityModel = $this->transformToModel($activity);
        $activityModel->shouldBeAnInstanceOf(MovementActivityFormModel::class);
        $activityModel->getType()->shouldReturn('Movement');
        $activityModel->getName()->shouldReturn('Running');
        $activityModel->getEnergy()->shouldReturn(500);
        $activityModel->getSpeedAverageMin()->shouldReturn(10.0);
        $activityModel->getSpeedAverageMax()->shouldReturn(15.0);
        $activityModel->getIntensity()->shouldReturn('Normal');
    }

    function it_should_be_able_to_transform_movement_activity_model_to_activity()
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

        $activity = $this->transformToActivity($activityModel);
        $activity->shouldBeAnInstanceOf(MovementActivity::class);
        $activity->getType()->shouldReturn('Movement');
        $activity->getName()->shouldReturn('Running');
        $activity->getEnergy()->shouldReturn(500);
        $activity->getSpeedAverageMin()->shouldReturn(10.0);
        $activity->getSpeedAverageMax()->shouldReturn(15.0);
        $activity->getIntensity()->shouldReturn('Normal');
    }

    function it_should_be_able_to_transform_movement_activity_model_to_activity_with_activity_given()
    {
        $activity = new MovementActivity();
        $activity
            ->setType('Movement')
            ->setName('another')
            ->setEnergy(1000)
            ->setSpeedAverageMin(17.0)
            ->setSpeedAverageMax(20.0)
            ->setIntensity('Very fast')
            ;

        $activityModel = new MovementActivityFormModel();
        $activityModel
            ->setType('Movement')
            ->setName('Running')
            ->setEnergy(500)
            ->setSpeedAverageMin(10.0)
            ->setSpeedAverageMax(15.0)
            ->setIntensity('Normal')
            ;

        $activity = $this->transformToActivity($activityModel, $activity);
        $activity->shouldBeAnInstanceOf(MovementActivity::class);
        $activity->getType()->shouldReturn('Movement');
        $activity->getName()->shouldReturn('Running');
        $activity->getEnergy()->shouldReturn(500);
        $activity->getSpeedAverageMin()->shouldReturn(10.0);
        $activity->getSpeedAverageMax()->shouldReturn(15.0);
        $activity->getIntensity()->shouldReturn('Normal');
    }

}

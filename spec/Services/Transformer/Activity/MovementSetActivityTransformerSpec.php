<?php

namespace spec\App\Services\Transformer\Activity;

use App\Services\Transformer\Activity\MovementSetActivityTransformer;
use App\Services\Transformer\Activity\ActivityTransformerInterface;
use App\Form\Model\Activity\MovementSetActivityFormModel;
use App\Entity\MovementSetActivity;
use PhpSpec\ObjectBehavior;

class MovementSetActivityTransformerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MovementSetActivityTransformer::class);
    }

    function it_impelements_activity_transformer_interface()
    {
        $this->shouldImplement(ActivityTransformerInterface::class);
    }

    function it_should_be_able_to_transform_movement_set_activity_to_model()
    {
        $activity = new MovementSetActivity();
        $activity
            ->setType('MovementSet')
            ->setName('Running circuits')
            ->setEnergy(1)
            ;

        $activityModel = $this->transformToModel($activity);
        $activityModel->shouldBeAnInstanceOf(MovementSetActivityFormModel::class);
        $activityModel->getType()->shouldReturn('MovementSet');
        $activityModel->getName()->shouldReturn('Running circuits');
        $activityModel->getEnergy()->shouldReturn(1);
    }

    function it_should_be_able_to_transform_movement_set_activity_model_to_activity()
    {
        $activityModel = new MovementSetActivityFormModel();
        $activityModel
            ->setType('MovementSet')
            ->setName('Running circuits')
            ;

        $activity = $this->transformToActivity($activityModel);
        $activity->shouldBeAnInstanceOf(MovementSetActivity::class);
        $activity->getType()->shouldReturn('MovementSet');
        $activity->getName()->shouldReturn('Running circuits');
        $activity->getEnergy()->shouldReturn(1);
    }

    function it_should_be_able_to_transform_movement_set_activity_model_to_activity_with_activity_given()
    {
        $activity = new MovementSetActivity();
        $activity
            ->setType('MovementSet')
            ->setName('another')
            ->setEnergy(200)
            ;

        $activityModel = new MovementSetActivityFormModel();
        $activityModel
            ->setType('MovementSet')
            ->setName('Running circuits')
            ;

        $activity = $this->transformToActivity($activityModel, $activity);
        $activity->shouldBeAnInstanceOf(MovementSetActivity::class);
        $activity->getType()->shouldReturn('MovementSet');
        $activity->getName()->shouldReturn('Running circuits');
        $activity->getEnergy()->shouldReturn(1);
    }

}

<?php

namespace spec\App\Services\Transformer\Activity;

use App\Services\Transformer\Activity\WeightActivityTransformer;
use App\Services\Transformer\Activity\ActivityTransformerInterface;
use App\Form\Model\Activity\WeightActivityFormModel;
use App\Entity\WeightActivity;
use PhpSpec\ObjectBehavior;

class WeightActivityTransformerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WeightActivityTransformer::class);
    }

    function it_impelements_activity_transformer_interface()
    {
        $this->shouldImplement(ActivityTransformerInterface::class);
    }

    function it_should_be_able_to_transform_weight_activity_to_model()
    {
        $activity = new WeightActivity();
        $activity
            ->setType('Weight')
            ->setName('Barbell Bench Press')
            ->setEnergy(300)
            ->setRepetitionsAvgMin(30)
            ->setRepetitionsAvgMax(60)
            ->setWeightAvgMin(30.0)
            ->setWeightAvgMax(50.0)
            ;

        $activityModel = $this->transformToModel($activity);
        $activityModel->shouldBeAnInstanceOf(WeightActivityFormModel::class);
        $activityModel->getType()->shouldReturn('Weight');
        $activityModel->getName()->shouldReturn('Barbell Bench Press');
        $activityModel->getEnergy()->shouldReturn(300);
        $activityModel->getRepetitionsAvgMin()->shouldReturn(30);
        $activityModel->getRepetitionsAvgMax()->shouldReturn(60);
        $activityModel->getWeightAvgMin()->shouldReturn(30.0);
        $activityModel->getWeightAvgMax()->shouldReturn(50.0);
        
    }

    function it_should_be_able_to_transform_weight_activity_model_to_activity()
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

        $activity = $this->transformToActivity($activityModel, null);
        $activity->shouldBeAnInstanceOf(WeightActivity::class);
        $activity->getType()->shouldReturn('Weight');
        $activity->getName()->shouldReturn('Barbell Bench Press');
        $activity->getEnergy()->shouldReturn(300);
        $activity->getRepetitionsAvgMin()->shouldReturn(30);
        $activity->getRepetitionsAvgMax()->shouldReturn(60);
        $activity->getWeightAvgMin()->shouldReturn(30.0);
        $activity->getWeightAvgMax()->shouldReturn(50.0);
    }

    function it_should_be_able_to_transform_weight_activity_model_to_activity_with_activity_given()
    {
        $activity = new WeightActivity();
        $activity
            ->setType('Weight')
            ->setName('Barbell Bench Press')
            ->setEnergy(300)
            ->setRepetitionsAvgMin(30)
            ->setRepetitionsAvgMax(60)
            ->setWeightAvgMin(30.0)
            ->setWeightAvgMax(50.0)
            ;

        $activityModel = new WeightActivityFormModel();
        $activityModel
            ->setType('Weight')
            ->setName('Standing Barbell Curl')
            ->setEnergy(500)
            ->setRepetitionsAvgMin(50)
            ->setRepetitionsAvgMax(80)
            ->setWeightAvgMin(80.0)
            ->setWeightAvgMax(120.0)
            ;

        $activity = $this->transformToActivity($activityModel, $activity);
        $activity->shouldBeAnInstanceOf(WeightActivity::class);
        $activity->getType()->shouldReturn('Weight');
        $activity->getName()->shouldReturn('Standing Barbell Curl');
        $activity->getEnergy()->shouldReturn(500);
        $activity->getRepetitionsAvgMin()->shouldReturn(50);
        $activity->getRepetitionsAvgMax()->shouldReturn(80);
        $activity->getWeightAvgMin()->shouldReturn(80.0);
        $activity->getWeightAvgMax()->shouldReturn(120.0);
    }

}
<?php

namespace spec\App\Services\Transformer\Activity;

use App\Services\Transformer\Activity\BodyweightActivityTransformer;
use App\Services\Transformer\Activity\ActivityTransformerInterface;
use App\Form\Model\Activity\BodyweightActivityFormModel;
use App\Entity\BodyweightActivity;
use PhpSpec\ObjectBehavior;

class BodyweightActivityTransformerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BodyweightActivityTransformer::class);
    }

    function it_impelements_activity_transformer_interface()
    {
        $this->shouldImplement(ActivityTransformerInterface::class);
    }

    function it_should_be_able_to_transform_bodyweight_activity_to_model()
    {
        $activity = new BodyweightActivity();
        $activity
            ->setType('Bodyweight')
            ->setName('Push-ups')
            ->setEnergy(500)
            ->setRepetitionsAvgMin(6)
            ->setRepetitionsAvgMax(12)
            ->setIntensity('Normal')
            ;

        $activityModel = $this->transformToModel($activity);
        $activityModel->shouldBeAnInstanceOf(BodyweightActivityFormModel::class);
        $activityModel->getType()->shouldReturn('Bodyweight');
        $activityModel->getName()->shouldReturn('Push-ups');
        $activityModel->getEnergy()->shouldReturn(500);
        $activityModel->getRepetitionsAvgMin()->shouldReturn(6);
        $activityModel->getRepetitionsAvgMax()->shouldReturn(12);
        $activityModel->getIntensity()->shouldReturn('Normal');
    }
    /* Integrity tests 
    function it_should_be_able_to_transform_array_to_bodyweight_activity_model()
    {
        $activityArray = [
            'type' => 'Bodyweight',
            'name' => 'Push-ups',
            'energy' => 500,
            'repetitionsAvgMin' => 6,
            'repetitionsAvgMax' => 12,
            'intensity' => 'Normal'
        ];

        $activityModel = $this->transformArrayToModel($activityArray);
        $activityModel->shouldBeAnInstanceOf(BodyweightActivityFormModel::class);
        $activityModel->getType()->shouldReturn('Bodyweight');
        $activityModel->getName()->shouldReturn('Push-ups');
        $activityModel->getEnergy()->shouldReturn(500);
        $activityModel->getRepetitionsAvgMin()->shouldReturn(6);
        $activityModel->getRepetitionsAvgMax()->shouldReturn(12);
        $activityModel->getIntensity()->shouldReturn('Normal');
    }
    */

    function it_should_be_able_to_transform_bodyweight_activity_model_to_activity()
    {
        $activityModel = new BodyweightActivityFormModel();
        $activityModel
            ->setType('Bodyweight')
            ->setName('Push-ups')
            ->setEnergy(500)
            ->setRepetitionsAvgMin(6)
            ->setRepetitionsAvgMax(12)
            ->setIntensity('Normal')
            ;

        $activity = $this->transformToActivity($activityModel, null);
        $activity->shouldBeAnInstanceOf(BodyweightActivity::class);
        $activity->getType()->shouldReturn('Bodyweight');
        $activity->getName()->shouldReturn('Push-ups');
        $activity->getEnergy()->shouldReturn(500);
        $activity->getRepetitionsAvgMin()->shouldReturn(6);
        $activity->getRepetitionsAvgMax()->shouldReturn(12);
        $activity->getIntensity()->shouldReturn('Normal');
    }

    function it_should_be_able_to_transform_bodyweight_activity_model_to_activity_with_activity_given()
    {
        $activity = new BodyweightActivity();
        $activity
            ->setType('Bodyweight')
            ->setName('another')
            ->setEnergy(1000)
            ->setRepetitionsAvgMin(13)
            ->setRepetitionsAvgMax(20)
            ->setIntensity('High')
            ;

        $activityModel = new BodyweightActivityFormModel();
        $activityModel
            ->setType('Bodyweight')
            ->setName('Push-ups')
            ->setEnergy(500)
            ->setRepetitionsAvgMin(6)
            ->setRepetitionsAvgMax(12)
            ->setIntensity('Normal')
            ;

        $activity = $this->transformToActivity($activityModel, $activity);
        $activity->shouldBeAnInstanceOf(BodyweightActivity::class);
        $activity->getType()->shouldReturn('Bodyweight');
        $activity->getName()->shouldReturn('Push-ups');
        $activity->getEnergy()->shouldReturn(500);
        $activity->getRepetitionsAvgMin()->shouldReturn(6);
        $activity->getRepetitionsAvgMax()->shouldReturn(12);
        $activity->getIntensity()->shouldReturn('Normal');
    }

}
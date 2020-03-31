<?php

namespace spec\App\Services\ModelExtender;

use App\Services\ModelExtender\WorkoutAverageExtender;
use App\Services\ModelExtender\WorkoutExtenderInterface;
use App\Form\Model\Workout\WorkoutAverageFormModel;
use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;
use App\Entity\MovementActivity;
use App\Entity\BodyweightActivity;
use App\Entity\MovementSetActivity;
use App\Entity\User;
use PhpSpec\ObjectBehavior;

class WorkoutAverageExtenderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WorkoutAverageExtender::class);
    }

    function it_impelements_workout_extender_interface()
    {
        $this->shouldImplement(WorkoutExtenderInterface::class);
    }

    function it_should_return_null_when_passed_unsupported_type_of_activity()
    {
        $activity = new MovementActivity();
        $activity
            ->setType('any')
            ;
        $workoutModel = new WorkoutAverageFormModel();
        $workoutModel
            ->setActivity($activity)
            ;

        $this->fillWorkoutModel($workoutModel, null)->shouldBe(null);
    }

    function it_is_able_to_extend_movement_workout_average_model_with_user()
    {   
        $user = new User();
        $activity = new MovementActivity();
        $activity
            ->setName('Running')
            ->setType('Movement')
            ->setEnergy(500)
            ->setSpeedAverageMin(10.0)
            ->setSpeedAverageMax(15.0)
            ;
        $workoutModel = new WorkoutAverageFormModel();
        $workoutModel
            ->setDurationSecondsTotal(3600)
            ->setActivity($activity)
            ;

        $workout = $this->fillWorkoutModel($workoutModel, $user);
        $workout->shouldBeAnInstanceOf(WorkoutAverageFormModel::class);
        $workout->getUser()->shouldBeAnInstanceOf(User::class);
        $workout->getActivity()->shouldBeAnInstanceOf(MovementActivity::class);
        $workout->getDurationSecondsTotal()->shouldReturn(3600);
        $workout->getDistanceTotal()->shouldReturn(12.5);
        $workout->getBurnoutEnergyTotal()->shouldReturn(500);
    }

    function it_is_able_to_extend_bodyweight_workout_average_model_with_user()
    {   
        $user = new User();
        $activity = new BodyweightActivity();
        $activity
            ->setName('Push-ups')
            ->setType('Bodyweight')
            ->setEnergy(500)
            ->setRepetitionsAvgMin(40)
            ->setRepetitionsAvgMax(60)
            ;
        $workoutModel = new WorkoutAverageFormModel();
        $workoutModel
            ->setDurationSecondsTotal(3600)
            ->setActivity($activity)
            ;

        $workout = $this->fillWorkoutModel($workoutModel, $user);
        $workout->shouldBeAnInstanceOf(WorkoutAverageFormModel::class);
        $workout->getUser()->shouldBeAnInstanceOf(User::class);
        $workout->getActivity()->shouldBeAnInstanceOf(BodyweightActivity::class);
        $workout->getDurationSecondsTotal()->shouldReturn(3600);
        $workout->getRepetitionsTotal()->shouldBeLike(50);
        $workout->getBurnoutEnergyTotal()->shouldReturn(500);
    }

    function it_is_able_to_extend_movement_set_workout_average_model_without_user()
    {   
        $activity = new MovementSetActivity();
        $activity
            ->setName('Running circuits')
            ->setType('MovementSet')
            ;

        $activityMovement = new MovementActivity();
        $activityMovement
            ->setEnergy(500)
            ->setSpeedAverageMin(10.0)
            ->setSpeedAverageMax(15.0)
            ;

        $movementSetModel = new MovementActivitySetFormModel();
        $movementSetModel
            ->setActivity($activityMovement)
            ->setDurationSeconds(3600)
            ;

        $workoutModel = new WorkoutAverageFormModel();
        $workoutModel
            ->setActivity($activity)
            ->addMovementSet($movementSetModel)
            ;

        $workout = $this->fillWorkoutModel($workoutModel, null);
        $workout->shouldBeAnInstanceOf(WorkoutAverageFormModel::class);
        $workout->getUser()->shouldBe(null);
        $workout->getActivity()->shouldBeAnInstanceOf(MovementSetActivity::class);
        $workout->getDurationSecondsTotal()->shouldReturn(3600);
        $workout->getDistanceTotal()->shouldReturn(12.5);
        $workout->getBurnoutEnergyTotal()->shouldReturn(500);
        $sets = $workout->getMovementSets();
        $sets[0]->shouldBeAnInstanceOf(MovementActivitySetFormModel::class);
        $sets[0]->getBurnoutEnergy()->shouldReturn(500);
        $sets[0]->getDurationSeconds()->shouldReturn(3600);
    }
}


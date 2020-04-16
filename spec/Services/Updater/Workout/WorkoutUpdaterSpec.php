<?php

namespace spec\App\Services\Updater\Workout;

use App\Services\Updater\Workout\WorkoutUpdater;
use App\Services\Updater\Workout\WorkoutUpdaterInterface;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;
use App\Entity\MovementActivity;
use App\Entity\MovementSetActivity;
use App\Entity\MovementSet;
use App\Entity\Workout;
use App\Entity\User;
use PhpSpec\ObjectBehavior;

class WorkoutUpdaterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WorkoutUpdater::class);
    }

    function it_impelements_workout_updater_interface()
    {
        $this->shouldImplement(WorkoutUpdaterInterface::class);
    }

    function it_should_be_able_to_update_movement_workout()
    {

        $user = new User();
        $activity = new MovementActivity();
        $activity
            ->setType('Movement')
            ;
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setBurnoutEnergyTotal(500)
            ->setStartAt(new \DateTime('2011-01-01'))
            ->setDistanceTotal(12.5)
            ->setImageFilename('test.jpeg')
            ;

        $workout = new Workout();
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(7200)
            ->setBurnoutEnergyTotal(1000)
            ->setStartAt(new \DateTime('2015-05-05'))
            ->setDistanceTotal(25)
            ->setImageFilename('oldtest.jpeg')
            ;

        $workoutUpdated = $this->update($workoutModel, $workout);
        $workoutUpdated->shouldBeAnInstanceOf(Workout::class);
        $workoutUpdated->getUser()->shouldBeAnInstanceOf(User::class);
        $workoutUpdated->getActivity()->shouldBeAnInstanceOf(MovementActivity::class);
        $workoutUpdated->getDurationSecondsTotal()->shouldReturn(3600);
        $workoutUpdated->getBurnoutEnergyTotal()->shouldReturn(500);
        $workoutUpdated->getStartAt()->shouldBeLike(new \DateTime('2011-01-01'));
        $workoutUpdated->getDistanceTotal()->shouldReturn(12.5);
        $workoutUpdated->getImageFilename()->shouldReturn('test.jpeg');
    }

    function it_should_be_able_to_change_movement_set_workout_to_movement_workout()
    {

        $user = new User();
        $activity = new MovementActivity();
        $activity
            ->setType('Movement')
            ;
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setBurnoutEnergyTotal(500)
            ->setStartAt(new \DateTime('2011-01-01'))
            ->setDistanceTotal(12.5)
            ->setImageFilename('test.jpeg')
            ;

        $movementSet = new MovementSet();
        $workout = new Workout();
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(7200)
            ->setBurnoutEnergyTotal(1000)
            ->setStartAt(new \DateTime('2015-05-05'))
            ->setDistanceTotal(25)
            ->addMovementSet($movementSet)
            ->setImageFilename('oldtest.jpeg')
            ;

        $workoutUpdated = $this->update($workoutModel, $workout);
        $workoutUpdated->shouldBeAnInstanceOf(Workout::class);
        $workoutUpdated->getUser()->shouldBeAnInstanceOf(User::class);
        $workoutUpdated->getActivity()->shouldBeAnInstanceOf(MovementActivity::class);
        $workoutUpdated->getDurationSecondsTotal()->shouldReturn(3600);
        $workoutUpdated->getBurnoutEnergyTotal()->shouldReturn(500);
        $workoutUpdated->getStartAt()->shouldBeLike(new \DateTime('2011-01-01'));
        $workoutUpdated->getDistanceTotal()->shouldReturn(12.5);
        $workoutUpdated->getImageFilename()->shouldReturn('test.jpeg');
        $sets = $workoutUpdated->getMovementSets();
        $sets[0]->shouldReturn(null);
    }

    function it_should_be_able_to_change_movement_workout_to_movement_set_workout()
    {
        $user = new User();
        $activity = new MovementActivity();
        $activity
            ->setType('Movement')
            ->setName('Running')
            ->setEnergy(500)
            ->setSpeedAverageMin(10.0)
            ->setSpeedAverageMax(15.0)
            ->setIntensity('Normal')
            ;

        //Model with new workout data 
        $movementSetActivity = new MovementSetActivity();
        $movementSetActivity
            ->setType('MovementSet')
            ->setName('Running circuits')
            ;
        $movementSetModel = new MovementActivitySetFormModel();
        $movementSetModel
            ->setActivity($activity)
            ->setDurationSeconds(3600)
            ->setBurnoutEnergy(500)
            ->setDistance(12.5)
            ;
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setUser($user)
            ->setActivity($movementSetActivity)
            ->setDurationSecondsTotal(3600)
            ->setBurnoutEnergyTotal(500)
            ->setStartAt(new \DateTime('2011-01-01'))
            ->setDistanceTotal(12.5)
            ->addMovementSet($movementSetModel)
            ->setImageFilename('test.jpeg')
            ;


        //Data belongs to old workout 
        $workout = new Workout();
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(7200)
            ->setBurnoutEnergyTotal(1000)
            ->setStartAt(new \DateTime('2015-05-05'))
            ->setDistanceTotal(25)
            ->setImageFilename('oldtest.jpeg')
            ;

        $workoutUpdated = $this->update($workoutModel, $workout);
        $workoutUpdated->shouldBeAnInstanceOf(Workout::class);
        $workoutUpdated->getUser()->shouldBeAnInstanceOf(User::class);
        $workoutUpdated->getActivity()->shouldBeAnInstanceOf(MovementSetActivity::class);
        $workoutUpdated->getDurationSecondsTotal()->shouldReturn(3600);
        $workoutUpdated->getBurnoutEnergyTotal()->shouldReturn(500);
        $workoutUpdated->getStartAt()->shouldBeLike(new \DateTime('2011-01-01'));
        $workoutUpdated->getDistanceTotal()->shouldReturn(12.5);
        $workoutUpdated->getImageFilename()->shouldReturn('test.jpeg');
        $sets = $workoutUpdated->getMovementSets();
        $sets[0]->getDurationSeconds()->shouldReturn(3600);
        $sets[0]->getBurnoutEnergy()->shouldReturn(500);
        $sets[0]->getDistance()->shouldReturn(12.5);
    }

    function it_should_throw_exception_when_new_activity_type_is_not_supported()
    {

        $user = new User();
        $activity = new MovementActivity();
        $activity
            ->setType('any')
            ;
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setBurnoutEnergyTotal(500)
            ->setStartAt(new \DateTime('2011-01-01'))
            ->setDistanceTotal(12.5)
            ->setImageFilename('test.jpeg')
            ;

        $movementSet = new MovementSet();
        $workout = new Workout();
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(7200)
            ->setBurnoutEnergyTotal(1000)
            ->setStartAt(new \DateTime('2015-05-05'))
            ->setDistanceTotal(25)
            ->addMovementSet($movementSet)
            ->setImageFilename('oldtest.jpeg')
            ;

        $this->shouldThrow('Exception')->during('update', [$workoutModel, $workout]);
    }
}

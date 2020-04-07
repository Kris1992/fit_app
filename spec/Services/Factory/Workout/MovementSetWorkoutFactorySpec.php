<?php

namespace spec\App\Services\Factory\Workout;

use App\Services\Factory\Workout\MovementSetWorkoutFactory;
use App\Services\Factory\Workout\WorkoutFactoryInterface;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;
use App\Entity\MovementSetActivity;
use App\Entity\MovementActivity;
use App\Entity\Workout;
use App\Entity\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MovementSetWorkoutFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MovementSetWorkoutFactory::class);
    }

    function it_impelements_activity_abstract_factory_interface()
    {
        $this->shouldImplement(WorkoutFactoryInterface::class);
    }

    function it_should_be_able_to_create_movement_set_workout()
    {   
        $workoutModel = new WorkoutSpecificFormModel();
        $movementActivity = new MovementActivity();
        $movementSetModel = new MovementActivitySetFormModel();

        $movementSetModel
            ->setWorkout($workoutModel)
            ->setActivity($movementActivity)
            ->setDistance(10.0)
            ->setDurationSeconds(3600)
            ->setBurnoutEnergy(500)
            ;

        $user = new User();
        $activity = new MovementSetActivity();
        
        $workoutModel
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setDistanceTotal(10.0)
            ->setBurnoutEnergyTotal(500)
            ->setStartAt(new \DateTime())
            ->addMovementSet($movementSetModel)
            ->setImageFilename('test.jpeg')
            ;

        $workout = $this->create($workoutModel);
        $workout->shouldBeAnInstanceOf(Workout::class);
        $workout->getUser()->shouldBeAnInstanceOf(User::class);
        $workout->getActivity()->shouldBeAnInstanceOf(MovementSetActivity::class);
        $workout->getDurationSecondsTotal()->shouldReturn(3600);
        $workout->getDistanceTotal()->shouldReturn(10.0);
        $workout->getBurnoutEnergyTotal()->shouldReturn(500);
        $workout->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
        $workout->getImageFilename()->shouldReturn('test.jpeg');
        
        $sets = $workout->getMovementSets();
        $sets[0]->getWorkout()->shouldBeAnInstanceOf(Workout::class);
        $sets[0]->getActivity()->shouldBeAnInstanceOf(MovementActivity::class);
        $sets[0]->getDistance()->shouldReturn(10.0);
        $sets[0]->getDurationSeconds()->shouldReturn(3600);
        $sets[0]->getBurnoutEnergy()->shouldReturn(500);
    }
}


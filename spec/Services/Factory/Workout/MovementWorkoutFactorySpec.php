<?php

namespace spec\App\Services\Factory\Workout;

use App\Services\Factory\Workout\MovementWorkoutFactory;
use App\Services\Factory\Workout\WorkoutFactoryInterface;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Entity\MovementActivity;
use App\Entity\Workout;
use App\Entity\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


class MovementWorkoutFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MovementWorkoutFactory::class);
    }

    function it_impelements_activity_abstract_factory_interface()
    {
        $this->shouldImplement(WorkoutFactoryInterface::class);
    }

    function it_should_be_able_to_create_movement_workout()
    {   
        $user = new User();
        $activity = new MovementActivity();
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setBurnoutEnergyTotal(500)
            ->setStartAt(new \DateTime())
            ;

        $workout = $this->create($workoutModel);
        $workout->shouldBeAnInstanceOf(Workout::class);
        $workout->getUser()->shouldBeAnInstanceOf(User::class);
        $workout->getActivity()->shouldBeAnInstanceOf(MovementActivity::class);
        $workout->getDurationSecondsTotal()->shouldReturn(3600);
        $workout->getBurnoutEnergyTotal()->shouldReturn(500);
        $workout->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
    }
}

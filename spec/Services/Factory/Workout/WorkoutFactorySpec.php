<?php

namespace spec\App\Services\Factory\Workout;

use App\Services\Factory\Workout\WorkoutFactory;
use App\Services\Factory\Workout\MovementWorkoutFactory;
use App\Services\Factory\Workout\MovementSetWorkoutFactory;
use App\Services\Factory\Workout\BodyweightWorkoutFactory;
use App\Services\Factory\Workout\WorkoutFactoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WorkoutFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WorkoutFactory::class);
    }

    function it_is_able_to_create_movement_workout_factory(){
        $this->beConstructedThrough('chooseFactory', ['Movement']);
        $this->shouldBeAnInstanceOf(MovementWorkoutFactory::class);
        $this->shouldImplement(WorkoutFactoryInterface::class);
    }

    function it_is_able_to_create_movement_set_workout_factory(){
        $this->beConstructedThrough('chooseFactory', ['MovementSet']);
        $this->shouldBeAnInstanceOf(MovementSetWorkoutFactory::class);
        $this->shouldImplement(WorkoutFactoryInterface::class);
    }

    function it_is_able_to_create_bodyweight_workout_factory(){
        $this->beConstructedThrough('chooseFactory', ['Bodyweight']);
        $this->shouldBeAnInstanceOf(BodyweightWorkoutFactory::class);
        $this->shouldImplement(WorkoutFactoryInterface::class);
    }

    function it_should_throw_exception_when_choosen_factory_does_not_exist(){
        $this->shouldThrow('Exception')->during('chooseFactory', [Argument::type('string')]);
    }
}



<?php

namespace spec\App\Services\Factory\WorkoutModel;

use App\Services\Factory\WorkoutModel\WorkoutModelFactory;
use App\Services\Factory\WorkoutModel\MovementWorkoutModelAverageFactory;
use App\Services\Factory\WorkoutModel\MovementSetWorkoutModelAverageFactory;
use App\Services\Factory\WorkoutModel\BodyweightWorkoutModelAverageFactory;
use App\Services\Factory\WorkoutModel\WeightWorkoutModelAverageFactory;
use App\Services\Factory\WorkoutModel\MovementWorkoutModelSpecificFactory;
use App\Services\Factory\WorkoutModel\MovementSetWorkoutModelSpecificFactory;
use App\Services\Factory\WorkoutModel\BodyweightWorkoutModelSpecificFactory;
use App\Services\Factory\WorkoutModel\WorkoutModelFactoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WorkoutModelFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WorkoutModelFactory::class);
    }

    //Avarage models
    function it_is_able_to_create_movement_workout_model_average_factory(){
        $this->beConstructedThrough('chooseFactory', ['Movement', 'Average']);
        $this->shouldBeAnInstanceOf(MovementWorkoutModelAverageFactory::class);
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_is_able_to_create_movement_set_workout_model_average_factory(){
        $this->beConstructedThrough('chooseFactory', ['MovementSet', 'Average']);
        $this->shouldBeAnInstanceOf(MovementSetWorkoutModelAverageFactory::class);
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_is_able_to_create_bodyweight_workout_model_average_factory(){
        $this->beConstructedThrough('chooseFactory', ['Bodyweight', 'Average']);
        $this->shouldBeAnInstanceOf(BodyweightWorkoutModelAverageFactory::class);
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_is_able_to_create_weight_workout_model_average_factory(){
        $this->beConstructedThrough('chooseFactory', ['Weight', 'Average']);
        $this->shouldBeAnInstanceOf(WeightWorkoutModelAverageFactory::class);
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    //Specific models
    function it_is_able_to_create_movement_workout_model_specific_factory(){
        $this->beConstructedThrough('chooseFactory', ['Movement', 'Specific']);
        $this->shouldBeAnInstanceOf(MovementWorkoutModelSpecificFactory::class);
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_is_able_to_create_movement_set_workout_model_specific_factory(){
        $this->beConstructedThrough('chooseFactory', ['MovementSet', 'Specific']);
        $this->shouldBeAnInstanceOf(MovementSetWorkoutModelSpecificFactory::class);
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_is_able_to_create_bodyweight_workout_model_specific_factory(){
        $this->beConstructedThrough('chooseFactory', ['Bodyweight', 'Specific']);
        $this->shouldBeAnInstanceOf(BodyweightWorkoutModelSpecificFactory::class);
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    //Unsupported
    function it_should_throw_exception_or_error_when_choosen_factory_does_not_exist(){
        //$this->shouldThrow('Exception')->during('chooseFactory', [Argument::type('string'), Argument::type('string')]);
        $this->shouldThrow('Exception')->during('chooseFactory', [Argument::type('string'), 'Average']);
        $this->shouldThrow('Exception')->during('chooseFactory', [Argument::type('string'), 'Specific']);
        $this->shouldThrow('Error')->during('chooseFactory', [Argument::type('string'), 'unsupported']);
    }
}


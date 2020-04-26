<?php

namespace spec\App\Services\Factory\Activity;

use App\Services\Factory\Activity\ActivityFactory;
use App\Services\Factory\Activity\ActivityAbstractFactory;
use App\Services\Factory\Activity\MovementActivityFactory;
use App\Services\Factory\Activity\MovementSetActivityFactory;
use App\Services\Factory\Activity\BodyweightActivityFactory;
use App\Services\Factory\Activity\WeightActivityFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ActivityFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ActivityFactory::class);
    }

    function it_is_able_to_create_movement_activity_factory(){
        $this->beConstructedThrough('chooseFactory', ['Movement']);
        $this->shouldBeAnInstanceOf(MovementActivityFactory::class);
        $this->shouldImplement(ActivityAbstractFactory::class);
    }

    function it_is_able_to_create_movement_set_activity_factory(){
        $this->beConstructedThrough('chooseFactory', ['MovementSet']);
        $this->shouldBeAnInstanceOf(MovementSetActivityFactory::class);
        $this->shouldImplement(ActivityAbstractFactory::class);
    }

    function it_is_able_to_create_bodyweight_activity_factory(){
        $this->beConstructedThrough('chooseFactory', ['Bodyweight']);
        $this->shouldBeAnInstanceOf(BodyweightActivityFactory::class);
        $this->shouldImplement(ActivityAbstractFactory::class);
    }

    function it_is_able_to_create_weight_activity_factory(){
        $this->beConstructedThrough('chooseFactory', ['Weight']);
        $this->shouldBeAnInstanceOf(WeightActivityFactory::class);
        $this->shouldImplement(ActivityAbstractFactory::class);
    }

    function it_should_throw_exception_when_choosen_factory_does_not_exist(){
        $this->shouldThrow('Exception')->during('chooseFactory', [Argument::type('string')]);
    }
}

<?php

namespace spec\App\Services\Transformer\Activity;

use App\Services\Transformer\Activity\ActivityTransformer;
use App\Services\Transformer\Activity\MovementActivityTransformer;
use App\Services\Transformer\Activity\MovementSetActivityTransformer;
use App\Services\Transformer\Activity\BodyweightActivityTransformer;
use App\Services\Transformer\Activity\ActivityTransformerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ActivityTransformerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ActivityTransformer::class);
    }

    function it_is_able_to_create_movement_activity_transformer(){
        $this->beConstructedThrough('chooseTransformer', ['Movement']);
        $this->shouldBeAnInstanceOf(MovementActivityTransformer::class);
        $this->shouldImplement(ActivityTransformerInterface::class);
    }

    function it_is_able_to_create_movement_set_activity_transformer(){
        $this->beConstructedThrough('chooseTransformer', ['MovementSet']);
        $this->shouldBeAnInstanceOf(MovementSetActivityTransformer::class);
        $this->shouldImplement(ActivityTransformerInterface::class);
    }

    function it_is_able_to_create_bodyweight_activity_transformer(){
        $this->beConstructedThrough('chooseTransformer', ['Bodyweight']);
        $this->shouldBeAnInstanceOf(BodyweightActivityTransformer::class);
        $this->shouldImplement(ActivityTransformerInterface::class);
    }

    function it_should_throw_exception_when_choosen_activity_transformer_does_not_exist(){
        $this->shouldThrow('Exception')->during('chooseTransformer', [Argument::type('string')]);
    }
}

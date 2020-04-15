<?php

namespace spec\App\Services\Converter;

use App\Form\Model\Activity\MovementActivityFormModel;
use App\Services\Converter\ArrayConverter;
use PhpSpec\ObjectBehavior;

class ArrayConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ArrayConverter::class);
    }

    function it_should_be_able_to_convert_array_to_movement_activity_form_model_object()
    {
        $arrayData = [
            'id' => 1,
            'type' => 'Movement',
            'name' => 'Running',
            'energy' => 500,
            'speedAverageMin' => 10.0,
            'speedAverageMax' => 14.0,
            'intensity' => 'Normal',
        ];

        $this->beConstructedThrough('toObject', [$arrayData, new MovementActivityFormModel()]);
        $this->shouldBeAnInstanceOf(MovementActivityFormModel::class);
        $this->getId()->shouldBe(1);
        $this->getType()->shouldReturn('Movement');
        $this->getName()->shouldReturn('Running');
        $this->getEnergy()->shouldReturn(500);
        $this->getSpeedAverageMin()->shouldReturn(10.0);
        $this->getSpeedAverageMax()->shouldReturn(14.0);
        $this->getIntensity()->shouldReturn('Normal');
    }

}

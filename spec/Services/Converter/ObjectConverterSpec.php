<?php

namespace spec\App\Services\Converter;

use App\Form\Model\Activity\MovementActivityFormModel;
use App\Services\Converter\ObjectConverter;
use PhpSpec\ObjectBehavior;

class ObjectConverterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ObjectConverter::class);
    }

    function it_should_be_able_to_convert_movement_activity_form_model_object_to_array()
    {
        $dataModel = new MovementActivityFormModel();
        $dataModel
            ->setId(1)
            ->setType('Movement')
            ->setName('Running')
            ->setEnergy(500)
            ->setSpeedAverageMin(10.0)
            ->setSpeedAverageMax(14.0)
            ->setIntensity('Normal')
            ;
        
        $array = $this->toArray($dataModel);
        $array->shouldBeArray();
        $array['id']->shouldBe(1);
        $array['type']->shouldBe('Movement');
        $array['name']->shouldBe('Running');
        $array['energy']->shouldBe(500);
        $array['speedAverageMin']->shouldBe(10.0);
        $array['speedAverageMax']->shouldBe(14.0);
        $array['intensity']->shouldBe('Normal');

    }
}

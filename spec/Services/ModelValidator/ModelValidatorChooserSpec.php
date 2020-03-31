<?php

namespace spec\App\Services\ModelValidator;

use App\Services\ModelValidator\ModelValidatorChooser;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ModelValidatorChooserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ModelValidatorChooser::class);
    }

    function it_is_able_to_choose_proper_validation_group()
    {   
        $validationGroup = $this->chooseValidationGroup('Bodyweight');
        $validationGroup->shouldReturn(['bodyweight_model']);

        $validationGroup2 = $this->chooseValidationGroup(Argument::type('string'));
        $validationGroup2->shouldReturn(['model']);
    }
}

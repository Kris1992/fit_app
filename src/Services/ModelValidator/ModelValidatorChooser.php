<?php 

namespace App\Services\ModelValidator;

/** 
 *  Choose group of validation
 */
class ModelValidatorChooser 
{
    public function chooseValidationGroup(string $type): array
    {   
        switch ($type) {
            case 'Bodyweight':
                return ['bodyweight_model'];
            case 'Weight':
                return ['weight_model'];
            default:
                return ['model'];
        }
    }

}
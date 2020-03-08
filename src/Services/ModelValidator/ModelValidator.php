<?php 

namespace App\Services\ModelValidator;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\ConstraintViolationList;

/** 
 *  Validator for model data
 */
class ModelValidator implements ModelValidatorInterface
{

    private $formErrors;

    private $validator;

    private $isValid;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * isValid Check is model data valid
     * @param  $dataModel Model data object which will be validated
     * @return bool
     */
    public function isValid($dataModel, $groups=null): bool
    {
        if ($groups === null) {
            $this->formErrors = $this->validator->validate($dataModel);
        } else {
            $this->formErrors = $this->validator->validate($dataModel, null, $groups);
        }

        
        if(count($this->formErrors) > 0) {
            return $this->isValid = false;
        }

        return $this->isValid = true;
    }

    /**
     * mapErrorsToForm Map Errors to given form 
     * @param  FormInterface $form Symfony form object
     * @return void
     */
    public function mapErrorsToForm(FormInterface $form): void
    {
        if (!$this->isValid) {
            foreach ($this->formErrors as $error) {
                $formError = new FormError($error->getMessage());
                $form->get($error->getPropertyPath())->addError($formError);
            }
        }
    }

    public function getErrors(): ConstraintViolationList
    {
        if (!$this->isValid) {
            return $this->formErrors;
        }
    }
}
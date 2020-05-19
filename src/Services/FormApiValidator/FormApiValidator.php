<?php 

namespace App\Services\FormApiValidator;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/** 
 *  Validator form data in api cases
 */
class FormApiValidator implements FormApiValidatorInterface
{

    public function getErrors(FormInterface $form)
    {   
        foreach ($form->getErrors() as $error) {
            return $error->getMessage();
        }

        $errors = array();
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childError = $this->getErrors($childForm)) {
                    $errors[$childForm->getName()] = $childError;
                }
            }
        }

        return $errors;
    }

}
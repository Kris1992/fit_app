<?php 
declare(strict_types=1);

namespace App\Services\FormApiValidator;

use Symfony\Component\Form\FormInterface;

/** 
 *  Validator form data in api cases 
 */
interface FormApiValidatorInterface
{

    /**
     * getErrors Get Errors from form to render without form binding
     *
     * @param FormInterface $form Form object after submit and isValid 
     */
    public function getErrors(FormInterface $form);

}

<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class ContainsAlphanumeric extends Constraint
{
    public $message = 'Password must contain at least 2 numbers and 3 letters!!';
}

?>
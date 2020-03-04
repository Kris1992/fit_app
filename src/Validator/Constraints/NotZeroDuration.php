<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class NotZeroDuration extends Constraint
{
    public $message = 'Duration cannot be zero';
}

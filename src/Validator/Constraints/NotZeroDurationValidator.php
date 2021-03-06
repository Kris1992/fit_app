<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NotZeroDurationValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {

        /* old one
        if (!$value instanceof \DateTime) {
            return;
        }
        $value = date_format($value, 'H:i');

        if (!$constraint instanceof NotZeroDuration) {
            throw new UnexpectedTypeException($constraint, NotZeroDuration::class);
        }
        
        if (null === $value || '' === $value) {
            return;
        }

        if ($value === '00:00') {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }*/

        if(!is_integer($value)) {
            return;
        }

        if (!$constraint instanceof NotZeroDuration) {
            throw new UnexpectedTypeException($constraint, NotZeroDuration::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if ($value === 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
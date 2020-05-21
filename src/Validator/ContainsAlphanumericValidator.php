<?php
declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ContainsAlphanumericValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ContainsAlphanumeric) {
            throw new UnexpectedTypeException($constraint, ContainsAlphanumeric::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');

            // separate multiple types using pipes
            // throw new UnexpectedValueException($value, 'string|int');
        }

        preg_match_all('/[a-zA-Z]/', $value, $letterMatches);
        preg_match_all('/[0-9]/', $value, $numberMatches);

        if(
            sizeof($letterMatches[0]) < 3 || 
            sizeof($numberMatches[0]) < 2
        ) {
            /* @var $constraint \App\Validator\ContainsAlphanumeric */
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
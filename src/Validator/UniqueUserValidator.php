<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use App\Repository\UserRepository;

class UniqueUserValidator extends ConstraintValidator
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($object, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueUser) {
            throw new UnexpectedTypeException($constraint, UniqueUser::class);
        }

        $field = $constraint->field;
        $method = 'get';
        $method .= ucfirst($field);
        
        $array[$field] = $object->$method();
        
        $existingUser = $this->userRepository->findOneBy($array);

        if (!$existingUser || $existingUser->getId() == $object->getId()) {
            return;
        }

        /* @var $constraint \App\Validator\UniqueUser */
        $this->context->buildViolation($constraint->message)
            ->atPath($constraint->errorPath)
            ->addViolation();
    }
}
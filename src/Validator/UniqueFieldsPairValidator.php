<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

class UniqueFieldsPairValidator extends ConstraintValidator
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($object, Constraint $constraint)
    {

        if (!$constraint instanceof UniqueFieldsPair) {
            throw new UnexpectedTypeException($constraint, UniqueFieldsPair::class);
        }

        if (count($constraint->fields) < 2) {
            throw new InvalidOptionsException('Expected array got string', $constraint->fields);
        }
        
        foreach ($constraint->fields as $key => $field) {
            $method = 'get';
            $method .= ucfirst($field);
        
            $array[$field] = $object->$method();
            
            //property cannot be null live it to notBlank assert
            if (null === $array[$field] || '' === $array[$field]) {
                return;
            }
        }

        $repository = $this->entityManager->getRepository('App\Entity\\'.$constraint->entityClass);

        $fieldsPairExist = $repository->findOneBy($array);

        if (!$fieldsPairExist || $fieldsPairExist->getId() == $object->getId()) {
            return;
        }

        /* @var $constraint \App\Validator\UniqueFieldsPair */
        $this->context->buildViolation($constraint->message)
            ->atPath($constraint->errorPath)
            ->addViolation();
    }
}
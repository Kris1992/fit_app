<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

class UniqueRangePropertiesValidator extends ConstraintValidator
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($object, Constraint $constraint)
    {

        if (!$constraint instanceof UniqueRangeProperties) {
            throw new UnexpectedTypeException($constraint, UniqueRangeProperties::class);
        }

        if (count($constraint->fields) < 2) {
            throw new InvalidOptionsException('Expected array got string', $constraint->fields);
        }


        $name = $object->getName();

        //property cannot be null live it to notBlank assert
        if (null === $name || '' === $name) {
            return;
        }

        foreach ($constraint->fields as $key => $field) {
            $method = 'get';
            $method .= ucfirst($field);
        
            $array[$key] = $object->$method();
            
            //property cannot be null live it to notBlank assert
            if (null === $array[$key] || '' === $array[$key]) {
                return;
            }
        }

        $repository = $this->entityManager->getRepository('App\Entity\\'.$constraint->entityClass);

        $rangeExist = $repository->findOneActivityWithRange(
            $name,
            $array[0],
            $array[1]
        );

        if (!$rangeExist || $rangeExist->getId() == $object->getId()) {
            return;
        }

        /* @var $constraint \App\Validator\UniqueRangeProperties */
        $this->context->buildViolation($constraint->message)
            ->atPath($constraint->errorPath)
            ->addViolation();
    }
}

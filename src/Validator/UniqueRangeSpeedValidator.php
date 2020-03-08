<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

//use App\Repository\MovementActivityRepository; // in future will be at least one more class with average speeds

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\InvalidOptionsException;

class UniqueRangeSpeedValidator extends ConstraintValidator
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($object, Constraint $constraint)
    {

        if (!$constraint instanceof UniqueRangeSpeed) {
            throw new UnexpectedTypeException($constraint, UniqueRangeSpeed::class);
        }

        if (count($constraint->fields) < 2) {
            throw new InvalidOptionsException('Expected array got string', $constraint->fields);
        }

        $name = $object->getName();
        $speedAverageMin = $object->getSpeedAverageMin();
        $speedAverageMax = $object->getSpeedAverageMax();

        //property cannot be null live it to notBlank assert
        if (
            null === $name || '' === $name ||
            null === $speedAverageMin || '' === $speedAverageMin ||
            null === $speedAverageMax || '' === $speedAverageMax
            ) {
            return;
        }

        $repository = $this->entityManager->getRepository('App\Entity\\'.$constraint->entityClass);

        $rangeExist = $repository->findOneActivityWithSpeedInRange(
            $object->getName(),
            $object->getSpeedAverageMin(),
            $object->getSpeedAverageMax()
        );

        if (!$rangeExist || $rangeExist->getId() == $object->getId()) {
            return;
        }

        /* @var $constraint \App\Validator\UniqueRangeSpeed */
        $this->context->buildViolation($constraint->message)
            ->atPath($constraint->errorPath)
            ->addViolation();
    }
}

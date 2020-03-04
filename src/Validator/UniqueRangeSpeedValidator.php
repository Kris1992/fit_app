<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use App\Repository\MovementActivityRepository;

class UniqueRangeSpeedValidator extends ConstraintValidator
{

    private $activityRepository;

    public function __construct(MovementActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        $rangeExist = $this->activityRepository->findOneActivityWithSpeedInRange(
            $value->getName(),
            $value->getSpeedAverageMin(),
            $value->getSpeedAverageMax()
        );

        if (!$rangeExist || $rangeExist->getId() == $value->getId()) {
            return;
        }

        /* @var $constraint \App\Validator\UniqueRangeSpeed */
        $this->context->buildViolation($constraint->message)
            ->atPath($constraint->errorPath)
            ->addViolation();
    }
}
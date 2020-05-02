<?php

namespace spec\App\Services\Factory\Workout;

use App\Services\Factory\Workout\WeightWorkoutFactory;
use App\Services\Factory\Workout\WorkoutFactoryInterface;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Entity\WeightActivity;
use App\Entity\Workout;
use App\Entity\User;
use PhpSpec\ObjectBehavior;

class WeightWorkoutFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WeightWorkoutFactory::class);
    }

    function it_impelements_activity_abstract_factory_interface()
    {
        $this->shouldImplement(WorkoutFactoryInterface::class);
    }

    function it_should_be_able_to_create_weight_workout()
    {   
        $user = new User();
        $activity = new WeightActivity();
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setRepetitionsTotal(50)
            ->setBurnoutEnergyTotal(500)
            ->setStartAt(new \DateTime())
            ->setDumbbellWeight(100.0)
            ->setImageFilename('test.jpeg')
            ;

        $workout = $this->create($workoutModel);
        $workout->shouldBeAnInstanceOf(Workout::class);
        $workout->getUser()->shouldBeAnInstanceOf(User::class);
        $workout->getActivity()->shouldBeAnInstanceOf(WeightActivity::class);
        $workout->getDurationSecondsTotal()->shouldReturn(3600);
        $workout->getRepetitionsTotal()->shouldReturn(50);
        $workout->getBurnoutEnergyTotal()->shouldReturn(500);
        $workout->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
        $workout->getDumbbellWeight()->shouldReturn(100.0);
        $workout->getImageFilename()->shouldReturn('test.jpeg');
    }
}

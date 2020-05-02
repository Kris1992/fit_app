<?php

namespace spec\App\Services\Factory\WorkoutModel;

use App\Services\Factory\WorkoutModel\WeightWorkoutModelSpecificFactory;
use App\Services\Factory\WorkoutModel\WorkoutModelFactoryInterface;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Entity\WeightActivity;
use App\Entity\Workout;
use App\Entity\User;
use PhpSpec\ObjectBehavior;

class WeightWorkoutModelSpecificFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WeightWorkoutModelSpecificFactory::class);
    }

    function it_impelements_workout_model_factory_interface()
    {
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_should_be_able_to_create_weight_workout_specific_model()
    {   

        $user = new User();
        $activity = new WeightActivity();
        $activity
            ->setName('Barbell Bench Press')
            ->setType('Weight')
            ;
        $workout = new Workout();
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setStartAt(new \DateTime())
            ->setRepetitionsTotal(40)
            ->setDumbbellWeight(30.0)
            ->setImageFilename('test.jpeg')
            ;

        $workoutModel = $this->create($workout);
        $workoutModel->shouldBeAnInstanceOf(WorkoutSpecificFormModel::class);
        $workoutModel->getUser()->shouldBeAnInstanceOf(User::class);
        $workoutModel->getActivityName()->shouldReturn('Barbell Bench Press');
        $workoutModel->getType()->shouldReturn('Weight');
        $workoutModel->getDurationSecondsTotal()->shouldReturn(3600);
        $workoutModel->getRepetitionsTotal()->shouldReturn(40);
        $workoutModel->getDumbbellWeight()->shouldReturn(30.0);
        $workoutModel->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
        $workoutModel->getImageFilename()->shouldReturn('test.jpeg');
    }
    
}

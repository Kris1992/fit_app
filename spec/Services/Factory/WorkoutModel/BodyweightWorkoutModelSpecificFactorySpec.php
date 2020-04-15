<?php

namespace spec\App\Services\Factory\WorkoutModel;

use App\Services\Factory\WorkoutModel\BodyweightWorkoutModelSpecificFactory;
use App\Services\Factory\WorkoutModel\WorkoutModelFactoryInterface;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Entity\BodyweightActivity;
use App\Entity\Workout;
use App\Entity\User;
use PhpSpec\ObjectBehavior;

class BodyweightWorkoutModelSpecificFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BodyweightWorkoutModelSpecificFactory::class);
    }

    function it_impelements_workout_model_factory_interface()
    {
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_should_be_able_to_create_bodyweight_workout_specific_model()
    {   

        $user = new User();
        $activity = new BodyweightActivity();
        $activity
            ->setName('Push-ups')
            ->setType('Bodyweight')
            ;
        $workout = new Workout();
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setStartAt(new \DateTime())
            ->setRepetitionsTotal(50)
            ->setImageFilename('test.jpeg')
            ;

        $workoutModel = $this->create($workout);
        $workoutModel->shouldBeAnInstanceOf(WorkoutSpecificFormModel::class);
        $workoutModel->getUser()->shouldBeAnInstanceOf(User::class);
        $workoutModel->getActivityName()->shouldReturn('Push-ups');
        $workoutModel->getType()->shouldReturn('Bodyweight');
        $workoutModel->getDurationSecondsTotal()->shouldReturn(3600);
        $workoutModel->getRepetitionsTotal()->shouldReturn(50);
        $workoutModel->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
        $workoutModel->getImageFilename()->shouldReturn('test.jpeg');
    }
}

<?php

namespace spec\App\Services\Factory\WorkoutModel;

use App\Services\Factory\WorkoutModel\MovementWorkoutModelSpecificFactory;
use App\Services\Factory\WorkoutModel\WorkoutModelFactoryInterface;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Entity\MovementActivity;
use App\Entity\Workout;
use App\Entity\User;
use PhpSpec\ObjectBehavior;

class MovementWorkoutModelSpecificFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MovementWorkoutModelSpecificFactory::class);
    }

    function it_impelements_workout_model_factory_interface()
    {
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_should_be_able_to_create_movement_workout_specific_model()
    {   

        $user = new User();
        $activity = new MovementActivity();
        $activity
            ->setName('Running')
            ->setType('Movement')
            ;
        $workout = new Workout();
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setStartAt(new \DateTime())
            ->setDistanceTotal(10.0)
            ;

        $workoutModel = $this->create($workout);
        $workoutModel->shouldBeAnInstanceOf(WorkoutSpecificFormModel::class);
        $workoutModel->getUser()->shouldBeAnInstanceOf(User::class);
        $workoutModel->getActivityName()->shouldReturn('Running');
        $workoutModel->getType()->shouldReturn('Movement');
        $workoutModel->getDurationSecondsTotal()->shouldReturn(3600);
        $workoutModel->getDistanceTotal()->shouldReturn(10.0);
        $workoutModel->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
    }
    
}

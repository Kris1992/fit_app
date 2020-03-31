<?php

namespace spec\App\Services\Factory\WorkoutModel;

use App\Services\Factory\WorkoutModel\BodyweightWorkoutModelAverageFactory;
use App\Services\Factory\WorkoutModel\WorkoutModelFactoryInterface;
use App\Form\Model\Workout\WorkoutAverageFormModel;
use App\Entity\BodyweightActivity;
use App\Entity\Workout;
use App\Entity\User;
use PhpSpec\ObjectBehavior;

class BodyweightWorkoutModelAverageFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BodyweightWorkoutModelAverageFactory::class);
    }

    function it_impelements_workout_model_factory_interface()
    {
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_should_be_able_to_create_bodyweight_workout_average_model()
    {   

        $user = new User();
        $activity = new BodyweightActivity();
        $workout = new Workout();
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setStartAt(new \DateTime())
            ;

        $workoutModel = $this->create($workout);
        $workoutModel->shouldBeAnInstanceOf(WorkoutAverageFormModel::class);
        $workoutModel->getUser()->shouldBeAnInstanceOf(User::class);
        $workoutModel->getActivity()->shouldBeAnInstanceOf(BodyweightActivity::class);
        $workoutModel->getDurationSecondsTotal()->shouldReturn(3600);
        $workoutModel->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
    }
}

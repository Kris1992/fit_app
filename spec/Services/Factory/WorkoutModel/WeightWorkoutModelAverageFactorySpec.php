<?php

namespace spec\App\Services\Factory\WorkoutModel;

use App\Services\Factory\WorkoutModel\WeightWorkoutModelAverageFactory;
use App\Services\Factory\WorkoutModel\WorkoutModelFactoryInterface;
use App\Form\Model\Workout\WorkoutAverageFormModel;
use App\Entity\WeightActivity;
use App\Entity\Workout;
use App\Entity\User;
use PhpSpec\ObjectBehavior;

class WeightWorkoutModelAverageFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WeightWorkoutModelAverageFactory::class);
    }

    function it_impelements_workout_model_factory_interface()
    {
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_should_be_able_to_create_weight_workout_average_model()
    {   
        $user = new User();
        $activity = new WeightActivity();
        $workout = new Workout();
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setRepetitionsTotal(50)
            ->setDumbbellWeight(100.0)
            ->setBurnoutEnergyTotal(500)
            ->setStartAt(new \DateTime())
            ->setImageFilename('test.jpeg')
            ;

        $workoutModel = $this->create($workout);
        $workoutModel->shouldBeAnInstanceOf(WorkoutAverageFormModel::class);
        $workoutModel->getUser()->shouldBeAnInstanceOf(User::class);
        $workoutModel->getActivity()->shouldBeAnInstanceOf(WeightActivity::class);
        $workoutModel->getDurationSecondsTotal()->shouldReturn(3600);
        $workoutModel->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
        $workoutModel->getImageFilename()->shouldReturn('test.jpeg');
    }
}

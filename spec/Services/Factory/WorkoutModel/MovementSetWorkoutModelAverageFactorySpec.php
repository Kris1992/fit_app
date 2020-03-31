<?php

namespace spec\App\Services\Factory\WorkoutModel;

use App\Services\Factory\WorkoutModel\MovementSetWorkoutModelAverageFactory;
use App\Services\Factory\WorkoutModel\WorkoutModelFactoryInterface;
use App\Form\Model\Workout\WorkoutAverageFormModel;
use App\Entity\MovementSetActivity;
use App\Entity\MovementActivity;
use App\Entity\MovementSet;
use App\Entity\Workout;
use App\Entity\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MovementSetWorkoutModelAverageFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MovementSetWorkoutModelAverageFactory::class);
    }

    function it_impelements_workout_model_factory_interface()
    {
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_should_be_able_to_create_movement_set_workout_average_model()
    {   

        $workout = new Workout();
        $movementActivity = new MovementActivity();
        $movementSet = new MovementSet();

        $movementSet
            ->setWorkout($workout)
            ->setActivity($movementActivity)
            ->setDistance(10.0)
            ->setDurationSeconds(3600)
            ->setBurnoutEnergy(500)
            ;

        $user = new User();
        $activity = new MovementSetActivity();
        $activity
            ->setType('MovementSet')
            ;
        
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setDurationSecondsTotal(3600)
            ->setStartAt(new \DateTime())
            ->addMovementSet($movementSet)
            ;
        
        $workoutModel = $this->create($workout);
        $workoutModel->shouldBeAnInstanceOf(WorkoutAverageFormModel::class);
        $workoutModel->getUser()->shouldBeAnInstanceOf(User::class);
        $workoutModel->getActivity()->shouldBeAnInstanceOf(MovementSetActivity::class);
        $workoutModel->getDurationSecondsTotal()->shouldReturn(3600);
        $workoutModel->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
        $workoutModel->getType()->shouldReturn('MovementSet');
        
        $sets = $workoutModel->getMovementSets();
        $sets[0]->getWorkout()->shouldBeAnInstanceOf(WorkoutAverageFormModel::class);
        $sets[0]->getActivity()->shouldBeAnInstanceOf(MovementActivity::class);
        $sets[0]->getDistance()->shouldReturn(10.0);
        $sets[0]->getDurationSeconds()->shouldReturn(3600);
        $sets[0]->getBurnoutEnergy()->shouldReturn(500);
    }
}

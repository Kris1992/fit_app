<?php

namespace spec\App\Services\Factory\WorkoutModel;

use App\Services\Factory\WorkoutModel\MovementSetWorkoutModelSpecificFactory;
use App\Services\Factory\WorkoutModel\WorkoutModelFactoryInterface;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Entity\MovementSetActivity;
use App\Entity\MovementActivity;
use App\Entity\MovementSet;
use App\Entity\Workout;
use App\Entity\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MovementSetWorkoutModelSpecificFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MovementSetWorkoutModelSpecificFactory::class);
    }

    function it_impelements_workout_model_factory_interface()
    {
        $this->shouldImplement(WorkoutModelFactoryInterface::class);
    }

    function it_should_be_able_to_create_movement_set_workout_specific_model()
    {   

        $workout = new Workout();
        $movementActivity = new MovementActivity();
        $movementActivity
            ->setName('Running')
            ;
        $movementSet = new MovementSet();

        $movementSet
            ->setWorkout($workout)
            ->setActivity($movementActivity)
            ->setDistance(10.0)
            ->setDurationSeconds(3600)
            ;

        $user = new User();
        $activity = new MovementSetActivity();
        $activity
            ->setName('Running circuits')
            ->setType('MovementSet')
            ;
        
        $workout
            ->setUser($user)
            ->setActivity($activity)
            ->setStartAt(new \DateTime())
            ->addMovementSet($movementSet)
            ->setImageFilename('test.jpeg')
            ;
        
        $workoutModel = $this->create($workout);
        $workoutModel->shouldBeAnInstanceOf(WorkoutSpecificFormModel::class);
        $workoutModel->getUser()->shouldBeAnInstanceOf(User::class);
        $workoutModel->getActivityName()->shouldReturn('Running circuits');
        $workoutModel->getStartAt()->shouldReturnAnInstanceOf('\DateTime');
        $workoutModel->getType()->shouldReturn('MovementSet');
        $workoutModel->getImageFilename()->shouldReturn('test.jpeg');
        
        $sets = $workoutModel->getMovementSets();
        $sets[0]->getWorkout()->shouldBeAnInstanceOf(WorkoutSpecificFormModel::class);
        $sets[0]->getActivityName()->shouldReturn('Running');
        $sets[0]->getDistance()->shouldReturn(10.0);
        $sets[0]->getDurationSeconds()->shouldReturn(3600);
    }
}

<?php

namespace spec\App\Services\ModelExtender;

use App\Services\ModelExtender\WorkoutSpecificExtender;
use App\Services\ModelExtender\WorkoutExtenderInterface;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;
use App\Repository\MovementActivityRepository;
use App\Repository\BodyweightActivityRepository;
use App\Repository\AbstractActivityRepository;
use Psr\Log\LoggerInterface;
use App\Entity\MovementActivity;
use App\Entity\BodyweightActivity;
use App\Entity\MovementSetActivity;
use App\Entity\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WorkoutSpecificExtenderSpec extends ObjectBehavior
{
    function let(MovementActivityRepository $movementRepository, AbstractActivityRepository $activityRepository, BodyweightActivityRepository $bodyweightRepository, LoggerInterface $logger)
    {
        $this->beConstructedWith($movementRepository, $activityRepository, $bodyweightRepository, $logger);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType(WorkoutSpecificExtender::class);
    }

    function it_impelements_workout_extender_interface()
    {
        $this->shouldImplement(WorkoutExtenderInterface::class);
    }

    function it_should_return_null_when_passed_unsupported_type_of_activity($logger)
    {
   
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setType('any')
            ;

        $this->fillWorkoutModel($workoutModel, null)->shouldBe(null);
        $logger->alert(Argument::type('string'))->shouldBeCalledTimes(1);
    }

    function it_is_able_to_extend_movement_workout_specific_model_with_user($movementRepository)
    {   
        $user = new User();

        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setActivityName('Running')
            ->setType('Movement')
            ->setDurationSecondsTotal(3600)
            ->setDistanceTotal(12.5)
            ;

        $activity = new MovementActivity();
        $activity
            ->setName('Running')
            ->setType('Movement')
            ->setEnergy(500)
            ->setSpeedAverageMin(10.0)
            ->setSpeedAverageMax(15.0)
            ;

        $movementRepository->findOneActivityBySpeedAverageAndName(
            Argument::type('string'),
            Argument::type('int')
        )->willReturn($activity);

        $workout = $this->fillWorkoutModel($workoutModel, $user);
        $workout->shouldBeAnInstanceOf(WorkoutSpecificFormModel::class);
        $workout->getUser()->shouldBeAnInstanceOf(User::class);
        $workout->getActivity()->shouldBeAnInstanceOf(MovementActivity::class);
        $workout->getDurationSecondsTotal()->shouldReturn(3600);
        $workout->getBurnoutEnergyTotal()->shouldReturn(500);
    }

    function it_should_return_null_when_not_find_movement_activity_in_database($movementRepository, $logger)
    {   
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setActivityName('Running')
            ->setType('Movement')
            ->setDurationSecondsTotal(3600)
            ->setDistanceTotal(12.5)
            ;

        $movementRepository->findOneActivityBySpeedAverageAndName(
            Argument::type('string'),
            Argument::type('int')
        )->willReturn(null);

        $this->fillWorkoutModel($workoutModel, null)->shouldBe(null);
        $logger->alert(Argument::type('string'))->shouldBeCalledTimes(1);
    }

    function it_is_able_to_extend_bodyweight_workout_specific_model_with_user($bodyweightRepository)
    {   
        $user = new User();
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setActivityName('Pushups')
            ->setType('Bodyweight')
            ->setDurationSecondsTotal(3600)
            ->setRepetitionsTotal(50)
            ;

        $activity = new BodyweightActivity();
        $activity
            ->setName('Pushups')
            ->setType('Bodyweight')
            ->setEnergy(500)
            ->setRepetitionsAvgMin(40)
            ->setRepetitionsAvgMax(60)
            ;

        $bodyweightRepository->findOneActivityByRepetitionsPerHourAndName(
            Argument::type('string'),
            Argument::type('int')
        )->willReturn($activity);

        $workout = $this->fillWorkoutModel($workoutModel, $user);
        $workout->shouldBeAnInstanceOf(WorkoutSpecificFormModel::class);
        $workout->getUser()->shouldBeAnInstanceOf(User::class);
        $workout->getActivity()->shouldBeAnInstanceOf(BodyweightActivity::class);
        $workout->getDurationSecondsTotal()->shouldReturn(3600);
        $workout->getBurnoutEnergyTotal()->shouldReturn(500);
    }

    function it_should_return_null_when_not_find_bodyweight_activity_in_database($bodyweightRepository, $logger)
    {   
        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setActivityName('Pushups')
            ->setType('Bodyweight')
            ->setDurationSecondsTotal(3600)
            ->setRepetitionsTotal(50)
            ;

        $bodyweightRepository->findOneActivityByRepetitionsPerHourAndName(
            Argument::type('string'),
            Argument::type('int')
        )->willReturn(null);

        $this->fillWorkoutModel($workoutModel, null)->shouldBe(null);
        $logger->alert(Argument::type('string'))->shouldBeCalledTimes(1);
    }

    function it_is_able_to_extend_movement_set_workout_specific_model_with_user($movementRepository, $activityRepository)
    {   
        $user = new User();
        $movementSetModel = new MovementActivitySetFormModel();
        $movementSetModel
            ->setActivityName('Running')
            ->setDurationSeconds(3600)
            ->setDistance(12.5)
            ;

        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setActivityName('Running circuits')
            ->setType('MovementSet')
            ->addMovementSet($movementSetModel)
            //->setDurationSecondsTotal(3600)
            //->setDistanceTotal(12.5)
            ;

        $activity = new MovementActivity();
        $activity
            ->setName('Running')
            ->setType('Movement')
            ->setEnergy(500)
            ->setSpeedAverageMin(10.0)
            ->setSpeedAverageMax(15.0)
            ;

        $movementRepository->findOneActivityBySpeedAverageAndName(
            Argument::type('string'),
            Argument::type('int')
        )->shouldBeCalledTimes(1)->willReturn($activity);

        $movementSetActivity = new MovementSetActivity();
        $movementSetActivity
            ->setName('Running circuits')
            ->setType('MovementSet')
            ;

        $activityRepository->findOneBy([
            'name' => 'Running circuits'
        ])->willReturn($movementSetActivity);

        $workout = $this->fillWorkoutModel($workoutModel, $user);
        $workout->shouldBeAnInstanceOf(WorkoutSpecificFormModel::class);
        $workout->getUser()->shouldBeAnInstanceOf(User::class);
        $workout->getActivity()->shouldBeAnInstanceOf(MovementSetActivity::class);
        $workout->getDurationSecondsTotal()->shouldReturn(3600);
        $workout->getBurnoutEnergyTotal()->shouldReturn(500);
        $workout->getDistanceTotal()->shouldReturn(12.5);
    }
    
    function it_should_return_null_when_not_find_movement_activity_in_database_when_try_create_movement_set($movementRepository, $logger)
    {   
        $movementSetModel = new MovementActivitySetFormModel();
        $movementSetModel
            ->setActivityName('Running')
            ->setDurationSeconds(3600)
            ->setDistance(12.5)
            ;

        $workoutModel = new WorkoutSpecificFormModel();
        $workoutModel
            ->setActivityName('Running circuits')
            ->setType('MovementSet')
            ->addMovementSet($movementSetModel)
            ;

        $movementRepository->findOneActivityBySpeedAverageAndName(
            Argument::type('string'),
            Argument::type('int')
        )->shouldBeCalledTimes(1)->willReturn(null);

        $this->fillWorkoutModel($workoutModel, null)->shouldBe(null);
        $logger->alert(Argument::type('string'))->shouldBeCalledTimes(1);
    }
}

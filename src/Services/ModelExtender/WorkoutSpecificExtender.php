<?php

namespace App\Services\ModelExtender;

use App\Entity\User;
use App\Entity\AbstractActivity;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Repository\MovementActivityRepository;
use App\Repository\AbstractActivityRepository;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\ImagesManager\ImagesManagerInterface;
use App\Services\FileDecoder\FileDecoderInterface;


use App\Repository\BodyweightActivityRepository;
use Psr\Log\LoggerInterface;

class WorkoutSpecificExtender implements WorkoutExtenderInterface {
    
    private $movementRepository;
    private $activityRepository;
    private $bodyweightRepository;
    private $workoutsImagesManager;
    private $base64Decoder;
    private $logger;

    /**
     * WorkoutSpecificExtender Constructor
     * @param MovementActivityRepository   $movementRepository   
     * @param AbstractActivityRepository   $activityRepository   
     * @param BodyweightActivityRepository $bodyweightRepository
     * @param ImagesManagerInterface       $workoutsImagesManager 
     * @param LoggerInterface              $logger               
     */
    public function __construct(
        MovementActivityRepository $movementRepository, 
        AbstractActivityRepository $activityRepository,
        BodyweightActivityRepository $bodyweightRepository,
        ImagesManagerInterface $workoutsImagesManager,
        FileDecoderInterface $base64Decoder,
        LoggerInterface $logger
    )
    {
        $this->movementRepository = $movementRepository;
        $this->activityRepository = $activityRepository;
        $this->bodyweightRepository = $bodyweightRepository;
        $this->workoutsImagesManager = $workoutsImagesManager;
        $this->base64Decoder = $base64Decoder;
        $this->logger = $logger;
    } 

    public function fillWorkoutModelWithMap(AbstractWorkoutFormModel $workoutModel, User $user, Array $data): ?AbstractWorkoutFormModel
    {   
        if ($data['image'] || $data['distanceTotal']) {
            $imageDestination = $this->workoutsImagesManager::WORKOUTS_IMAGES.'/'.$user->getLogin().'/';
            $filePath = $this->base64Decoder->decode($data['image'], $imageDestination);
            if ($filePath) {
                //$mapImage = new File($filePath);
                $workoutModel->setDistanceTotal($data['distanceTotal']);

                try {
                    $newFilename = $this->workoutsImagesManager->resizeImageFromPath($filePath, 150);
                } catch (\Exception $e) {
                    return null;
                }
                $workoutModel->setImageFilename($newFilename);

                return $this->fillWorkoutModel($workoutModel, $user, null);
            }
        }

        return null;
    }

    public function fillWorkoutModel(AbstractWorkoutFormModel $workoutModel, ?User $user, ?File $image): ?AbstractWorkoutFormModel
    {
        if ($user) {
            $workoutModel                    
                ->setUser($user);
        }

        switch ($workoutModel->getType()) {
            case 'Movement':
                $workoutModel = $this->fillMovementProperties($workoutModel);
                break;
            case 'MovementSet':
                $workoutModel = $this->fillMovementSetProperties($workoutModel);
                break;
            case 'Bodyweight':
                $workoutModel = $this->fillBodyweightProperties($workoutModel);
                break;
            default:
                $this->logger->alert(sprintf('Workout specific extender catched try of expend unsupported activity type name: %s!!', $workoutModel->getType()));
                return null;
        }

        if ($image) {
            $subdirectory = $workoutModel->getUser()->getLogin();
            $newFilename = $this->workoutsImagesManager->uploadImage($image, $workoutModel->getImageFilename(), $subdirectory);
            $workoutModel->setImageFilename($newFilename);
        }

        return $workoutModel;
    }

    private function fillMovementProperties(AbstractWorkoutFormModel $workoutModel): ?AbstractWorkoutFormModel
    {

        /*$activity = $this->movementRepository->findOneActivityBySpeedAverageAndName(
            $workoutModel->getActivityName(),
            $workoutModel->getAverageSpeed()
        );*/
        $activity = $this->getMovementActivity(
            $workoutModel->getActivityName(),
            $workoutModel->getAverageSpeed()
        );

        if ($activity) {
            $workoutModel                    
                ->setActivity($activity)
                ->calculateSaveBurnoutEnergyTotal()
                ;

            return $workoutModel;
        }

        $this->logger->alert(sprintf('Workout specific extender catched try of expend unsupported activity with name "%s" and average speed "%s" !!',$workoutModel->getActivityName(), $workoutModel->getAverageSpeed()));

        return null;
    }

    private function fillMovementSetProperties(AbstractWorkoutFormModel $workoutModel): ?AbstractWorkoutFormModel
    {
        
        $durationSecondsTotal = 0;
        $burnoutEnergyTotal = 0;
        $distanceTotal = 0;

        $movementSetCollection = $workoutModel->getMovementSets();
        foreach ($movementSetCollection as $movementSet) {
            $activity = $this->getMovementActivity(
                $movementSet->getActivityName(),
                $movementSet->getAverageSpeed()
            );
            if ($activity) {
                $movementSet->setActivity($activity);
                $movementSet->calculateSaveBurnoutEnergy();
                $durationSecondsTotal += $movementSet->getDurationSeconds();
                $burnoutEnergyTotal += $movementSet->getBurnoutEnergy();
                $distanceTotal += $movementSet->getDistance();
            } else {
                $message = sprintf('Data about activity with name "%s" and average speed "%s" do not exist in our database. Contact with admin.',$movementSet->getActivityName(), $movementSet->getAverageSpeed());
                $this->logger->alert($message);
                //throw new \Exception($message);
                return null;
            }
        }

        $workoutActivity = $this->activityRepository->findOneBy([
            'name' => $workoutModel->getActivityName()
        ]);
        
        $workoutModel
            ->setActivity($workoutActivity)
            ->setDurationSecondsTotal($durationSecondsTotal)
            ->setBurnoutEnergyTotal($burnoutEnergyTotal)
            ->setDistanceTotal($distanceTotal)
            ;
         
        return $workoutModel;
    }

    private function fillBodyweightProperties(AbstractWorkoutFormModel $workoutModel): ?AbstractWorkoutFormModel
    {

        $activity = $this->getBodyweightActivity(
            $workoutModel->getActivityName(),
            $workoutModel->getRepetitionsPerHour()
        );

        if ($activity) {
            $workoutModel                    
                ->setActivity($activity)
                ->calculateSaveBurnoutEnergyTotal()
                ;

            return $workoutModel;
        }

        $this->logger->alert(sprintf('Workout specific extender catched try of expend unsupported activity with name "%s" and average repetitions "%s" !!',$workoutModel->getActivityName(), $workoutModel->getRepetitionsPerHour()));

        return null;
    }

    /**
     * getMovementActivity Get movement activity by given name and average speed 
     * @param  string $activityName Name of activity
     * @param  int    $averageSpeed Average speed
     * @return AbstractActivity|null
     */
    private function getMovementActivity(string $activityName, int $averageSpeed): ?AbstractActivity
    {
        return $this->movementRepository->findOneActivityBySpeedAverageAndName(
            $activityName,
            $averageSpeed
        );
    }

    /**
     * getBodyweightActivity Get bodyweight activity by given name and repetitions by hour 
     * @param  string $activityName Name of activity
     * @param  int    $repetitionsPerHour Average repetitions per hour
     * @return AbstractActivity|null
     */
    private function getBodyweightActivity(string $activityName, int $repetitionsPerHour): ?AbstractActivity
    {
        return $this->bodyweightRepository->findOneActivityByRepetitionsPerHourAndName(
            $activityName,
            $repetitionsPerHour
        );
    }

}
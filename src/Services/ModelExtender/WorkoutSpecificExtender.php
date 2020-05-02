<?php

namespace App\Services\ModelExtender;

use App\Entity\User;
use App\Entity\AbstractActivity;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
use App\Form\Model\Workout\RouteDataModel;
use App\Repository\MovementActivityRepository;
use App\Repository\AbstractActivityRepository;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\ImagesManager\ImagesManagerInterface;
use App\Services\FileDecoder\FileDecoderInterface;
use App\Repository\BodyweightActivityRepository;
use App\Repository\WeightActivityRepository;
use Psr\Log\LoggerInterface;
use App\Services\WeatherService\WeatherServiceInterface;

//Rozbić to na mniejsze klasy
class WorkoutSpecificExtender implements WorkoutExtenderInterface {
    
    private $movementRepository;
    private $activityRepository;
    private $bodyweightRepository;
    private $weightRepository;
    private $workoutsImagesManager;
    private $base64Decoder;
    private $weatherService;
    private $logger;

    /**
     * WorkoutSpecificExtender Constructor
     * @param MovementActivityRepository   $movementRepository   
     * @param AbstractActivityRepository   $activityRepository   
     * @param BodyweightActivityRepository $bodyweightRepository
     * @param WeightActivityRepository $weightRepository
     * @param ImagesManagerInterface       $workoutsImagesManager 
     * @param LoggerInterface              $logger               
     */
    public function __construct(
        MovementActivityRepository $movementRepository, 
        AbstractActivityRepository $activityRepository,
        BodyweightActivityRepository $bodyweightRepository,
        WeightActivityRepository $weightRepository,
        ImagesManagerInterface $workoutsImagesManager,
        FileDecoderInterface $base64Decoder,
        WeatherServiceInterface $weatherService,
        LoggerInterface $logger
    )
    {
        $this->movementRepository = $movementRepository;
        $this->activityRepository = $activityRepository;
        $this->bodyweightRepository = $bodyweightRepository;
        $this->weightRepository = $weightRepository;
        $this->workoutsImagesManager = $workoutsImagesManager;
        $this->base64Decoder = $base64Decoder;
        $this->weatherService = $weatherService;
        $this->logger = $logger;
    } 
    //Dodać testy do tej metody
    public function fillWorkoutModelWithMap(AbstractWorkoutFormModel $workoutModel, User $user, Array $data): ?AbstractWorkoutFormModel
    {   
        if ($data['distanceTotal']) {
            $workoutModel->setDistanceTotal($data['distanceTotal']);
        }
        if ($data['routeData']) {
            $workoutModel = $this->setRouteData($workoutModel, $data);
        }
        if ($data['image']) {
            $imageDestination = $this->workoutsImagesManager::WORKOUTS_IMAGES.'/'.$user->getLogin().'/';
            $filePath = $this->base64Decoder->decode($data['image'], $imageDestination);
            if ($filePath) {
                //$mapImage = new File($filePath);
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
            case 'Weight':
                $workoutModel = $this->fillWeightProperties($workoutModel);
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

        $this->logger->alert(sprintf('Workout specific extender catched try of expend unsupported activity with name "%s" and average repetitions "%d" !!',$workoutModel->getActivityName(), $workoutModel->getRepetitionsPerHour()));

        return null;
    }

    private function fillWeightProperties(AbstractWorkoutFormModel $workoutModel): ?AbstractWorkoutFormModel
    {

        $activity = $this->getWeightActivity(
            $workoutModel->getActivityName(),
            $workoutModel->getRepetitionsPerHour(),
            $workoutModel->getDumbbellWeight()
        );

        if ($activity) {
            $workoutModel                    
                ->setActivity($activity)
                ->calculateSaveBurnoutEnergyTotal()
                ;

            return $workoutModel;
        }

        $this->logger->alert(sprintf('Workout specific extender catched try of expend unsupported activity with name "%s" and average repetitions "%d"  and weight "%d"!!',$workoutModel->getActivityName(), $workoutModel->getRepetitionsPerHour(), $workoutModel->getDumbbellWeight()));

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

    /**
     * getWeightActivity Get weight activity by given name and repetitions by hour and dumbbell weight 
     * @param  string $activityName Name of activity
     * @param  int    $repetitionsPerHour Average repetitions per hour
     * @param  float    $dumbbellWeight Average weight of dumbbell
     * @return AbstractActivity|null
     */
    private function getWeightActivity(string $activityName, int $repetitionsPerHour, float $dumbbellWeight): ?AbstractActivity
    {
        return $this->weightRepository->findOneActivityByRepetitionsPerHourAndWeightAndName(
            $activityName,
            $repetitionsPerHour,
            $dumbbellWeight
        );
    }

    private function setRouteData(AbstractWorkoutFormModel $workoutModel, Array $data)
    {
        $altitudeMax = null;
        $altitudeMin = null;
        $position = null;

        foreach ($data['routeData'] as $route) {
            $routeData = explode(',', $route);
            if (!$altitudeMax && !$altitudeMin) {
                $altitudeMax = $routeData[2];
                $altitudeMin = $routeData[2];
                $position = [
                    'lat' => $routeData[0],
                    'lng' => $routeData[1]
                ];
            } 
            if ($routeData[2] < $altitudeMin) {
                $altitudeMin = $routeData[2];
            }
            if ($routeData[2] > $altitudeMax) {
                $altitudeMax = $routeData[2];
            }
        }

        if (strpos($data['formData']['startAt'], 'T')) {
            $date = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $data['formData']['startAt']);
        } else {
            $date = \DateTime::createFromFormat('Y-m-d G:i', $data['formData']['startAt']);
        }
        
        $weatherData = $this->weatherService->getWeather($position, $date);

        $routeDataModel = new RouteDataModel();
        $routeDataModel
            ->setAltitudeMin($altitudeMin)
            ->setAltitudeMax($altitudeMax)
            ;

        if ($weatherData) {
            $routeDataModel
                ->setTemperature($weatherData['temperature'])
                ->setWeatherConditions($weatherData['weatherConditions'])
                ;
        }

        $workoutModel->setRouteData($routeDataModel);
        
        return $workoutModel;
    }

}
<?php
declare(strict_types=1);

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
use App\Services\ModelExtender\WorkoutSpecificStrategy\BodyweightWorkoutStrategy;
use App\Services\ModelExtender\WorkoutSpecificStrategy\WeightWorkoutStrategy;
use App\Services\ModelExtender\WorkoutSpecificStrategy\MovementWorkoutStrategy;
use App\Services\ModelExtender\WorkoutSpecificStrategy\MovementSetWorkoutStrategy;
use App\Services\ModelExtender\WorkoutSpecificStrategy\SpecificExtender;
use App\Services\ModelExtender\WorkoutImageFiller\WorkoutImageFiller;

class WorkoutSpecificExtender implements WorkoutExtenderInterface 
{
    
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
    
    //Dodać testy do tej metody (nadal mi ta klasa nie za bardzo pasuje w takiej formie)
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
                $strategy = new MovementWorkoutStrategy($this->movementRepository, $this->logger);
                break;
            case 'MovementSet':
                $strategy = new MovementSetWorkoutStrategy($this->activityRepository, $this->movementRepository, $this->logger);
                break;
            case 'Bodyweight':
                $strategy = new BodyweightWorkoutStrategy($this->bodyweightRepository, $this->logger);
                break;
            case 'Weight':
                $strategy = new WeightWorkoutStrategy($this->weightRepository, $this->logger);
                break;
            default:
                $this->logger->alert(sprintf('Workout specific extender catched try of expend unsupported activity type name: %s!!', $workoutModel->getType()));
                return null;
        }

        $specificExtender = new SpecificExtender($workoutModel, $strategy);
        $workoutModel = $specificExtender->getFilledWorkoutModel();

        if ($image) {
            $workoutModel = WorkoutImageFiller::fill($this->workoutsImagesManager, $workoutModel, $image);
        }

        return $workoutModel;
    }

    private function setRouteData(AbstractWorkoutFormModel $workoutModel, Array $data)
    {
        $altitudeMax = null;
        $altitudeMin = null;
        $position = null;

        foreach ($data['routeData'] as $route) {
            $routeData = explode(',', $route);
            if (!$altitudeMax && !$altitudeMin) {
                $altitudeMax = floatval($routeData[2]);
                $altitudeMin = floatval($routeData[2]);
                $position = [
                    'lat' => $routeData[0],
                    'lng' => $routeData[1]
                ];
            } 
            if ($routeData[2] < $altitudeMin) {
                $altitudeMin = floatval($routeData[2]);
            }
            if ($routeData[2] > $altitudeMax) {
                $altitudeMax = floatval($routeData[2]);
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

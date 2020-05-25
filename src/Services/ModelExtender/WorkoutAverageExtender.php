<?php
declare(strict_types=1);

namespace App\Services\ModelExtender;

use App\Entity\User;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\ImagesManager\ImagesManagerInterface;
use App\Services\ModelExtender\WorkoutAverageStrategy\AverageExtender;
use App\Services\ModelExtender\WorkoutAverageStrategy\BodyweightWorkoutStrategy;
use App\Services\ModelExtender\WorkoutAverageStrategy\MovementSetWorkoutStrategy;
use App\Services\ModelExtender\WorkoutAverageStrategy\MovementWorkoutStrategy;
use App\Services\ModelExtender\WorkoutAverageStrategy\WeightWorkoutStrategy;

class WorkoutAverageExtender implements WorkoutExtenderInterface 
{

    private $workoutsImagesManager;

    /**
     * WorkoutAverageExtender Constructor
     * 
     * @param ImagesManagerInterface $workoutsImagesManager
     */
    public function __construct(ImagesManagerInterface $workoutsImagesManager)  
    {
        $this->workoutsImagesManager = $workoutsImagesManager;
    }

    public function fillWorkoutModel(AbstractWorkoutFormModel $workoutModel, ?User $user, ?File $image): ?AbstractWorkoutFormModel
    {

        $activity = $workoutModel->getActivity();
        $workoutModel
            ->setType($activity->getType());
        if ($user) {
            $workoutModel                    
                ->setUser($user);
        }

        switch ($activity->getType()) {
            case 'Movement':
                $strategy = new MovementWorkoutStrategy();
                break;
            case 'MovementSet':
                $strategy = new MovementSetWorkoutStrategy();
                break;
            case 'Bodyweight':
                $strategy = new BodyweightWorkoutStrategy();
                break;
            case 'Weight':
                $strategy = new WeightWorkoutStrategy();
                break;
            default:
                return null;
        }

        $averageExtender = new AverageExtender($workoutModel, $strategy);
        $workoutModel = $averageExtender->getFilledWorkoutModel();

        if ($image) {
            $subdirectory = $workoutModel->getUser()->getLogin();
            $newFilename = $this->workoutsImagesManager->uploadImage($image, $workoutModel->getImageFilename(), $subdirectory);
            $workoutModel->setImageFilename($newFilename);
        }

        return $workoutModel;
    }

    public function fillWorkoutModelWithMap(AbstractWorkoutFormModel $workoutModel, User $user, Array $data): ?AbstractWorkoutFormModel
    {
        
    }
    
}

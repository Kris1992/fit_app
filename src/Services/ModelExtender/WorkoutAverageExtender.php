<?php

namespace App\Services\ModelExtender;

use App\Entity\User;
use App\Form\Model\Workout\AbstractWorkoutFormModel;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\ImagesManager\ImagesManagerInterface;

class WorkoutAverageExtender implements WorkoutExtenderInterface {

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

    public function fillWorkoutModelWithMap(AbstractWorkoutFormModel $workoutModel, User $user, Array $data): ?AbstractWorkoutFormModel
    {
        
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
                $workoutModel = $this->fillMovementProperties($workoutModel);
                break;
            case 'MovementSet':
                $workoutModel = $this->fillMovementSetProperties($workoutModel);
                break;
            case 'Bodyweight':
                $workoutModel = $this->fillBodyweightProperties($workoutModel);
                break;
            default:
                return null;
        }

        if ($image) {
            $subdirectory = $workoutModel->getUser()->getLogin();
            $newFilename = $this->workoutsImagesManager->uploadImage($image, $workoutModel->getImageFilename(), $subdirectory);
            $workoutModel->setImageFilename($newFilename);
        }

        return $workoutModel;
    }

    private function fillMovementProperties(AbstractWorkoutFormModel $workoutModel): AbstractWorkoutFormModel
    {

        $workoutModel
            ->calculateSaveBurnoutEnergyTotal()
            ->calculateSaveDistanceTotal()
            ;

        return $workoutModel;
    }

    private function fillBodyweightProperties(AbstractWorkoutFormModel $workoutModel): AbstractWorkoutFormModel
    {

        $workoutModel
            ->calculateSaveBurnoutEnergyTotal()
            ->calculateSaveRepetitionsTotal()
            ;

        return $workoutModel;
    }

    private function fillMovementSetProperties(AbstractWorkoutFormModel $workoutModel): AbstractWorkoutFormModel
    {
        $durationSecondsTotal = 0;
        $burnoutEnergyTotal = 0;
        $distanceTotal = 0;

        $movementSetCollection = $workoutModel->getMovementSets();
        foreach ($movementSetCollection as $movementSet) {
            $movementSet
                ->calculateSaveBurnoutEnergy()
                ->calculateSaveDistance()
                ;
            $durationSecondsTotal += $movementSet->getDurationSeconds();
            $burnoutEnergyTotal += $movementSet->getBurnoutEnergy();
            $distanceTotal += $movementSet->getDistance();
        }
        
        $workoutModel
            ->setDurationSecondsTotal($durationSecondsTotal)
            ->setBurnoutEnergyTotal($burnoutEnergyTotal)
            ->setDistanceTotal($distanceTotal)
            ;
            
        return $workoutModel;
    }
}
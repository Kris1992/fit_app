<?php
declare(strict_types=1);

namespace App\Services\ModelExtender\WorkoutImageFiller;

use App\Form\Model\Workout\AbstractWorkoutFormModel;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\ImagesManager\ImagesManagerInterface;

class WorkoutImageFiller
{

    public function fill(ImagesManagerInterface $workoutsImagesManager, AbstractWorkoutFormModel $workoutModel, File $image): AbstractWorkoutFormModel
    {

        $subdirectory = $workoutModel->getUser()->getLogin();
        $newFilename = $workoutsImagesManager->uploadImage($image, $workoutModel->getImageFilename(), $subdirectory);
        $workoutModel->setImageFilename($newFilename);

        return $workoutModel;

    }
    
}

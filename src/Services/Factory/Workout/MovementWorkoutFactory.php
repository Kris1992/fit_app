<?php

namespace App\Services\Factory\Workout;

use App\Entity\Workout;
use App\Entity\RouteData;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

class MovementWorkoutFactory implements WorkoutFactoryInterface {

    //Poprawić testy
    public function create(AbstractWorkoutFormModel $workoutModel): Workout
    {
        $workout = new Workout();
        $workout
            ->setUser($workoutModel->getUser())
            ->setActivity($workoutModel->getActivity())
            ->setDurationSecondsTotal($workoutModel->getDurationSecondsTotal())
            ->setDistanceTotal($workoutModel->getDistanceTotal())
            ->setBurnoutEnergyTotal($workoutModel->getBurnoutEnergyTotal())
            ->setStartAt($workoutModel->getStartAt())
            ->setImageFilename($workoutModel->getImageFilename())
            ;
   
        if ($workoutModel->getRouteData()) {
            $routeDataModel = $workoutModel->getRouteData();
            $routeData = new RouteData();
            $routeData
                ->setTemperature($routeDataModel->getTemperature())
                ->setWeatherConditions($routeDataModel->getWeatherConditions())
                ->setAltitudeMin($routeDataModel->getAltitudeMin())
                ->setAltitudeMax($routeDataModel->getAltitudeMax())
                ;
            $workout->setRouteData($routeData);
        }

        return $workout;
    }
}
<?php

namespace App\Services\Updater\Workout;

use App\Entity\Workout;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

class WorkoutUpdater implements WorkoutUpdaterInterface {

    public function update(AbstractWorkoutFormModel $dataModel, Workout $workout): Workout
    {
        $activity = $dataModel->getActivity();
        switch ($activity->getType()) {
            case 'Movement':
                return $this->updateMovementType($dataModel, $workout);
            default:
                # code...
                break;
        }
    }

    private function updateMovementType(AbstractWorkoutFormModel $dataModel, Workout $workout)
    {
        //inside this function we can check update is needed (is something changed?), but for now it is ok
        $workout
            ->setUser($dataModel->getUser())
            ->setActivity($dataModel->getActivity())
            ->setBurnoutEnergyTotal($dataModel->getBurnoutEnergyTotal())
            ->setStartAt($dataModel->getStartAt())
            ->setDurationSecondsTotal($dataModel->getDurationSecondsTotal())
            ->setDistanceTotal($dataModel->getDistanceTotal())
            ;

        return $workout;
    }

}
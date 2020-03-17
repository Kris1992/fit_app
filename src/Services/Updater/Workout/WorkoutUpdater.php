<?php

namespace App\Services\Updater\Workout;

use App\Entity\Workout;
use App\Entity\MovementSet;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

class WorkoutUpdater implements WorkoutUpdaterInterface {

    const MOVEMENT_UPDATER="Movement";
    const MOVEMENTSET_UPDATER="MovementSet";

    public function update(AbstractWorkoutFormModel $dataModel, Workout $workout): Workout
    {
        $activity = $dataModel->getActivity();
        switch ($activity->getType()) {
            case self::MOVEMENT_UPDATER:
                return $this->updateMovementType($dataModel, $workout);
            case self::MOVEMENTSET_UPDATER:
                return $this->updateMovementSetType($dataModel, $workout);
            default:
                return null;
        }
    }

    private function updateMovementType(AbstractWorkoutFormModel $dataModel, Workout $workout)
    {
        //First check workout for sets data (If user change activity from sets to another one)
        if ($this->checkIsChangeFromSets($workout)) {
            $workout = $this->deleteSetsData($workout);
        }

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

    private function updateMovementSetType(AbstractWorkoutFormModel $dataModel, Workout $workout)
    {
        $workoutMovementSets = $workout->getMovementSets();
        $modelMovementSets = $dataModel->getMovementSets();

        //update or remove section
        $keysArray = $workoutMovementSets->getKeys();
        foreach ($keysArray as $key) {
            $workoutMovementSet = $workoutMovementSets->get($key);
            $modelMovementSet = $modelMovementSets->get($key);
            if($modelMovementSet) {
                if ($workoutMovementSet->getId() === $modelMovementSet->getId()) {
                    $workoutMovementSet
                        ->setActivity($modelMovementSet->getActivity())
                        ->setDistance($modelMovementSet->getDistance())
                        ->setDurationSeconds($modelMovementSet->getDurationSeconds())
                        ->setBurnoutEnergy($modelMovementSet->getBurnoutEnergy())
                        ;
                }
            } else {
                $workout
                    ->removeMovementSet($workoutMovementSet);
            }
        }

        //create new
        foreach ($modelMovementSets as $modelMovementSet) {
            if ($modelMovementSet->getId() === null) {
                $movementSet = new MovementSet();
                $movementSet
                    ->setWorkout($workout)
                    ->setActivity($modelMovementSet->getActivity())
                    ->setDistance($modelMovementSet->getDistance())
                    ->setDurationSeconds($modelMovementSet->getDurationSeconds())
                    ->setBurnoutEnergy($modelMovementSet->getBurnoutEnergy())
                    ;
                
                $workout
                    ->addMovementSet($movementSet);
            }
        }
        
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

    /**
     * checkIsChangeFromSets Check workout was changed from sets activity
     * @param  Workout $workout Workout entity which will be checked
     * @return bool 
     */
    private function checkIsChangeFromSets(Workout $workout): bool
    {
        $workoutMovementSets = $workout->getMovementSets();
        if ($workoutMovementSets) {
            return true;
        }

        return false;
    }

    /**
     * deleteSetsData Delete all sets data from workout
     * @param  Workout $workout Workout entity to clear sets data
     * @return Workout
     */
    private function deleteSetsData(Workout $workout): Workout
    {
        $workoutMovementSets = $workout->getMovementSets();
        if ($workoutMovementSets) {
            foreach ($workoutMovementSets as $workoutMovementSet) {
                $workout->removeMovementSet($workoutMovementSet);
            }
        }

        return $workout;
    }

}
<?php

namespace App\Services\Updater\Workout;

use App\Entity\Workout;
use App\Entity\MovementSet;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

class WorkoutUpdater implements WorkoutUpdaterInterface {

    const MOVEMENT_UPDATER="Movement";
    const MOVEMENTSET_UPDATER="MovementSet";
    const BODYWEIGHT_UPDATER="Bodyweight";

    public function update(AbstractWorkoutFormModel $dataModel, Workout $workout): Workout
    {
        $activity = $dataModel->getActivity();
        switch ($activity->getType()) {
            case self::MOVEMENT_UPDATER:
                return $this->updateMovementType($dataModel, $workout);
            case self::MOVEMENTSET_UPDATER:
                return $this->updateMovementSetType($dataModel, $workout);
            case self::BODYWEIGHT_UPDATER:
                return $this->updateBodyweightType($dataModel, $workout);
            default:
                throw new \Exception('Unsupported type of activity');
        }
    }

    private function updateMovementType(AbstractWorkoutFormModel $dataModel, Workout $workout): Workout
    {
        //Prevent clear unused data
        $workout = $this->deleteOldWorkoutData($workout, ['MovementSet', 'Bodyweight']);

        //inside this function we can check update is needed (is something changed?), but for now it is ok
        $workout
            ->setUser($dataModel->getUser())
            ->setActivity($dataModel->getActivity())
            ->setBurnoutEnergyTotal($dataModel->getBurnoutEnergyTotal())
            ->setStartAt($dataModel->getStartAt())
            ->setDurationSecondsTotal($dataModel->getDurationSecondsTotal())
            ->setDistanceTotal($dataModel->getDistanceTotal())
            ->setImageFilename($dataModel->getImageFilename())
            ;

        return $workout;
    }

    private function updateBodyweightType(AbstractWorkoutFormModel $dataModel, Workout $workout): Workout
    {
        //Prevent clear unused data
        $workout = $this->deleteOldWorkoutData($workout, ['Movement', 'MovementSet']);

        $workout
            ->setUser($dataModel->getUser())
            ->setActivity($dataModel->getActivity())
            ->setBurnoutEnergyTotal($dataModel->getBurnoutEnergyTotal())
            ->setStartAt($dataModel->getStartAt())
            ->setDurationSecondsTotal($dataModel->getDurationSecondsTotal())
            ->setRepetitionsTotal($dataModel->getRepetitionsTotal())
            ->setImageFilename($dataModel->getImageFilename())
            ;

        return $workout;
    }

    private function updateMovementSetType(AbstractWorkoutFormModel $dataModel, Workout $workout): Workout
    {
        //Prevent clear unused data
        $workout = $this->deleteOldWorkoutData($workout, ['Bodyweight']);

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
            ->setImageFilename($dataModel->getImageFilename())
            ;
        
        return $workout;
    }

    /**
     * deleteMovementSetsData Delete all movement sets data from workout
     * @param  Workout $workout Workout entity to clear movement sets data
     * @return Workout
     */
    private function deleteMovementSetsData(Workout $workout): Workout
    {
        $workoutMovementSets = $workout->getMovementSets();
        if ($workoutMovementSets) {
            foreach ($workoutMovementSets as $workoutMovementSet) {
                $workout->removeMovementSet($workoutMovementSet);
            }
        }

        return $workout;
    }

    /**
     * deleteOldWorkoutData Delete all unused data from previous workout
     * @param  Workout $workout Workout entity to clear unused data
     * @return Workout
     */
    private function deleteOldWorkoutData(Workout $workout, Array $types): Workout
    {
        foreach ($types as $type) {
            switch ($type) {
                case 'Movement':
                    $workout
                        ->setDistanceTotal(null);
                    break;
                case 'MovementSet':
                    $workout = $this->deleteMovementSetsData($workout);
                    break;
                case 'Bodyweight':
                    $workout
                        ->setRepetitionsTotal(null);
                default:
                    break;
            }
        }

        return $workout;
    }
}
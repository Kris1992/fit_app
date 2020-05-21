<?php
declare(strict_types=1);

namespace App\Form\Model\Workout;

use Symfony\Component\Validator\Constraints as Assert;

class WorkoutAverageFormModel extends AbstractWorkoutFormModel
{

    /**
     * @Assert\NotBlank(message="Cannot calculate right distance", groups={"model"})
     * @Assert\GreaterThan(message="Calculated distance it's not correct", value=0, groups={"model"})
     */
    private $distanceTotal;

    /**
     * @Assert\NotBlank(message="Cannot calculate right number of repetitions", 
     * groups={"bodyweight_model", "weight_model"})
     * @Assert\GreaterThan(message="Calculated number of repetitions it's not correct", 
     * value=0, groups={"bodyweight_model", "weight_model"})
     */
    private $repetitionsTotal;

    /**
     * @Assert\NotBlank(message="Cannot calculate right dumbbell weight", groups={"weight_model"})
     * @Assert\GreaterThan(message="Calculated dumbbell weight it's not correct", value=0, groups={"weight_model"})
     */
    private $dumbbellWeight;

    public function setDistanceTotal(?float $distanceTotal): self
    {
        $this->distanceTotal = $distanceTotal;

        return $this;
    }    

    public function getDistanceTotal(): ?float
    {
        return $this->distanceTotal;
    }

    public function calculateSaveDistanceTotal(): self
    {
        $speedDiff = $this->activity->getSpeedAverageMax() - $this->activity->getSpeedAverageMin();
        $speed = $this->activity->getSpeedAverageMin() + ($speedDiff / 2);

        $distanceTotal = $speed * ($this->getDurationSecondsTotal() / 3600);

        $this->distanceTotal = $distanceTotal;

        return $this;
    }

    public function setRepetitionsTotal(?int $repetitionsTotal): self
    {
        $this->repetitionsTotal = $repetitionsTotal;

        return $this;
    }    

    public function getRepetitionsTotal(): ?int
    {
        return $this->repetitionsTotal;
    }

    public function calculateSaveRepetitionsTotal(): self
    {
        $repetitionsDiff = $this->activity->getRepetitionsAvgMax() - $this->activity->getRepetitionsAvgMin();
        $repetitionsPerHour = $this->activity->getRepetitionsAvgMin() + ($repetitionsDiff / 2);

        $repetitionsTotal = $repetitionsPerHour * ($this->getDurationSecondsTotal() / 3600);

        $this->repetitionsTotal = intval($repetitionsTotal);

        return $this;
    }

    public function getDumbbellWeight(): ?float
    {
        return $this->dumbbellWeight;
    }

    public function setDumbbellWeight(?float $dumbbellWeight): self
    {
        $this->dumbbellWeight = $dumbbellWeight;

        return $this;
    }

    public function calculateSaveDumbbellWeight(): self
    {
        $dumbbellWeightDiff = $this->activity->getWeightAvgMax() - $this->activity->getWeightAvgMin();
        $dumbbellWeight = $this->activity->getWeightAvgMin() + ($dumbbellWeightDiff / 2);

        $this->dumbbellWeight = intval($dumbbellWeight);

        return $this;
    }

}

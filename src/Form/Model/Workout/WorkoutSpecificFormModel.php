<?php

namespace App\Form\Model\Workout;

use Symfony\Component\Validator\Constraints as Assert;

class WorkoutSpecificFormModel extends AbstractWorkoutFormModel
{

    /**
     * @Assert\NotBlank(message="Please enter distance", groups={"movement", "model", "route_model"})
     * @Assert\GreaterThan(value=0, groups={"movement", "model", "route_model"})
     */
    private $distanceTotal;

    /**
     * @Assert\NotBlank(message="Please enter number of repetitions", 
     * groups={"bodyweight_model", "weight"})
     * @Assert\GreaterThan(value=0, groups={"bodyweight_model", "weight"})
     */
    private $repetitionsTotal;

    /**
     * @Assert\NotBlank(message="Please enter dumbbell weight", groups={"weight"})
     * @Assert\GreaterThan(message="Dumbbell weight should be greater than 0", value=0, groups={"weight"})
     */
    private $dumbbellWeight;

    public function getDistanceTotal(): ?float
    {
        return $this->distanceTotal;
    }

    public function setDistanceTotal(?float $distanceTotal): self
    {
        $this->distanceTotal = $distanceTotal;

        return $this;
    }

    public function getRepetitionsTotal(): ?int
    {
        return $this->repetitionsTotal;
    }

    public function setRepetitionsTotal(?int $repetitionsTotal): self
    {
        $this->repetitionsTotal = $repetitionsTotal;

        return $this;
    }

    public function getRepetitionsPerHour(): ?int
    {
        if($this->getRepetitionsTotal() != null && $this->getDurationSecondsTotal() != null){
            return (int)($this->getRepetitionsTotal()/($this->getDurationSecondsTotal()/3600));
        }

        return null;
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

    public function getAverageSpeed(): ?float
    {
        if($this->getDistanceTotal() != null && $this->getDurationSecondsTotal() != null){
            return $this->getDistanceTotal()/($this->getDurationSecondsTotal()/3600);
        }

        return null;
    }

}

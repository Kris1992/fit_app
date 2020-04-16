<?php
namespace App\Form\Model\Workout;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;

class WorkoutAverageFormModel extends AbstractWorkoutFormModel
{

    /**
     * @Assert\NotBlank(message="Cannot calculate right distance", groups={"model"})
     * @Assert\GreaterThan(message="Calculated distance it's not correct", value=0, groups={"model"})
     */
    private $distanceTotal;

    /**
     * @Assert\NotBlank(message="Cannot calculate right number of repetitions", 
     * groups={"bodyweight_model"})
     * @Assert\GreaterThan(message="Calculated number of repetitions it's not correct", 
     * value=0, groups={"bodyweight_model"})
     */
    private $repetitionsTotal;

    /**
     * @Assert\Valid
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "You must specify at least one set",
     *      groups={"average_sets"}
     * )
     */
    private $movementSets;

    public function __construct()
    {
        $this->movementSets = new ArrayCollection();
    }


    /**
     * @return Collection|MovementActivitySetFormModel[]
     */
    public function getMovementSets(): Collection
    {
        return $this->movementSets;
    }

    public function addMovementSet(MovementActivitySetFormModel $movementSet): self
    {
        if (!$this->movementSets->contains($movementSet)) {
            $this->movementSets[] = $movementSet;
            $movementSet->setWorkout($this);
        }

        return $this;
    }

    public function removeMovementSet(MovementActivitySetFormModel $movementSet): self
    {
        if ($this->movementSets->contains($movementSet)) {
            $this->movementSets->removeElement($movementSet);
            // set the owning side to null (unless already changed)
            if ($movementSet->getWorkout() === $this) {
                $movementSet->setWorkout(null);
            }
        }

        return $this;
    }

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

    public function getRepetitionsTotal(): ?float
    {
        return $this->repetitionsTotal;
    }

    public function calculateSaveRepetitionsTotal(): self
    {
        $repetitionsDiff = $this->activity->getRepetitionsAvgMax() - $this->activity->getRepetitionsAvgMin();
        $repetitionsPerHour = $this->activity->getRepetitionsAvgMin() + ($repetitionsDiff / 2);

        $repetitionsTotal = $repetitionsPerHour * ($this->getDurationSecondsTotal() / 3600);

        $this->repetitionsTotal = (int)$repetitionsTotal;

        return $this;
    }

}

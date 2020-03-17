<?php
namespace App\Form\Model\Workout;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;

class WorkoutSpecificFormModel extends AbstractWorkoutFormModel
{

    /**
     * @Assert\NotBlank(message="Please enter distance", groups={"movement", "model"})
     * @Assert\GreaterThan(value=0, groups={"movement", "model"})
     */
    private $distanceTotal;

    /**
     * @Assert\Valid
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "You must specify at least one set",
     *      groups={"specific_sets"}
     * )
     */
    private $movementSets;

    public function __construct()
    {
        $this->movementSets = new ArrayCollection();
    }


    public function getDistanceTotal(): ?int
    {
        return $this->distanceTotal;
    }

    public function setDistanceTotal(int $distanceTotal): self
    {
        $this->distanceTotal = $distanceTotal;

        return $this;
    }

    public function getAverageSpeed(): ?float
    {
        if($this->getDistanceTotal() != null && $this->getDurationSecondsTotal() != null){
            return $this->getDistanceTotal()/($this->getDurationSecondsTotal()/3600);
        }

        return null;
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

}

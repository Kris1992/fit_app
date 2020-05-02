<?php
namespace App\Form\Model\Workout;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;

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

    /**
     * @Assert\Valid
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "You must specify at least one set",
     *      groups={"specific_sets"}
     * )
     */
    private $movementSets;

    private $routeData;

    public function __construct()
    {
        $this->movementSets = new ArrayCollection();
    }

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

    public function getRouteData(): ?RouteDataModel
    {
        return $this->routeData;
    }

    public function setRouteData(?RouteDataModel $routeData): self
    {
        $this->routeData = $routeData;

        return $this;
    }

}

<?php
namespace App\Form\Model\Workout;

use Symfony\Component\Validator\Constraints as Assert;

class WorkoutSpecificFormModel extends AbstractWorkoutFormModel
{

    /**
     * @Assert\NotBlank(message="Please enter distance", groups={"movement"})
     * @Assert\GreaterThan(value=0, groups={"movement"})
     */
    private $distanceTotal;


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

}

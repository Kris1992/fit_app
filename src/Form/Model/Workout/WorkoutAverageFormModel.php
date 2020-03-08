<?php
namespace App\Form\Model\Workout;

use Symfony\Component\Validator\Constraints as Assert;

class WorkoutAverageFormModel extends AbstractWorkoutFormModel
{

    /**
     * @Assert\NotBlank(message="Cannot calculate right distance", groups={"model"})
     * @Assert\GreaterThan(message="Calculated distance it's not correct", value=0, groups={"model"})
     */
    private $distanceTotal;


    public function getDistanceTotal(): ?int
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


}

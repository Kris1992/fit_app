<?php
declare(strict_types=1);

namespace App\Form\Model\Workout\WorkoutSet;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Movement version of set activity model to WorkoutModels
 */
class MovementActivitySetFormModel extends AbstractActivitySetFormModel
{

    /**
     * @Assert\NotBlank(message="Distance cannot be blank", groups={"model", "specific_sets"})
     * @Assert\GreaterThan(message="Distance must be greater than 0", value=0, groups={"model", 
     * "specific_sets"})
     */
    private $distance;


    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(?float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function calculateSaveDistance(): self
    {
        $speedDiff = $this->activity->getSpeedAverageMax() - $this->activity->getSpeedAverageMin();
        $speed = $this->activity->getSpeedAverageMin() + ($speedDiff / 2);

        $distance = $speed * ($this->getDurationSeconds() / 3600);

        $this->distance = $distance;

        return $this;
    }

    public function getAverageSpeed(): ?float
    {
        if($this->getDistance() != null && $this->getDurationSeconds() != null){
            return $this->getDistance()/($this->getDurationSeconds()/3600);
        }

        return null;
    }

    
}

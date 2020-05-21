<?php
declare(strict_types=1);

namespace App\Form\Model\Activity;

use Symfony\Component\Validator\Constraints as Assert;

class WeightActivityFormModel extends AbstractActivityFormModel
{

    /**
     * @Assert\NotBlank(message="Please enter number of repetitions")
     */
    private $repetitionsAvgMin;

    /**
     * @Assert\NotBlank(message="Please enter number of repetitions")
     * @Assert\GreaterThan(
     *     propertyPath="repetitionsAvgMin",
     *     message="Largest average number of repetitions must be greater than lowest"
     *     )
     */
    private $repetitionsAvgMax;

    /**
     * @Assert\NotBlank(message="Please enter number of repetitions")
     */
    private $weightAvgMin;

    /**
     * @Assert\NotBlank(message="Please enter number of repetitions")
     * @Assert\GreaterThan(
     *     propertyPath="weightAvgMin",
     *     message="Largest average weight must be greater than lowest"
     *     )
     */
    private $weightAvgMax;

    public function getRepetitionsAvgMin(): ?int
    {
        return $this->repetitionsAvgMin;
    }

    public function setRepetitionsAvgMin(?int $repetitionsAvgMin): self
    {
        $this->repetitionsAvgMin = $repetitionsAvgMin;

        return $this;
    }

    public function getRepetitionsAvgMax(): ?int
    {
        return $this->repetitionsAvgMax;
    }

    public function setRepetitionsAvgMax(?int $repetitionsAvgMax): self
    {
        $this->repetitionsAvgMax = $repetitionsAvgMax;

        return $this;
    }

    public function getWeightAvgMin(): ?float
    {
        return $this->weightAvgMin;
    }

    public function setWeightAvgMin(?float $weightAvgMin): self
    {
        $this->weightAvgMin = $weightAvgMin;

        return $this;
    }

    public function getWeightAvgMax(): ?float
    {
        return $this->weightAvgMax;
    }

    public function setWeightAvgMax(?float $weightAvgMax): self
    {
        $this->weightAvgMax = $weightAvgMax;

        return $this;
    }
}

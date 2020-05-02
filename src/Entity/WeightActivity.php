<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WeightActivityRepository")
 */
class WeightActivity extends AbstractActivity
{

    /**
     * @ORM\Column(type="integer")
     */
    protected $repetitionsAvgMin;

    /**
     * @ORM\Column(type="integer")
     */
    protected $repetitionsAvgMax;

    /**
     * @ORM\Column(type="float")
     */
    protected $weightAvgMin;

    /**
     * @ORM\Column(type="float")
     */
    protected $weightAvgMax;

    public function getRepetitionsAvgMin(): ?int
    {
        return $this->repetitionsAvgMin;
    }

    public function setRepetitionsAvgMin(int $repetitionsAvgMin): self
    {
        $this->repetitionsAvgMin = $repetitionsAvgMin;

        return $this;
    }

    public function getRepetitionsAvgMax(): ?int
    {
        return $this->repetitionsAvgMax;
    }

    public function setRepetitionsAvgMax(int $repetitionsAvgMax): self
    {
        $this->repetitionsAvgMax = $repetitionsAvgMax;

        return $this;
    }

    public function getWeightAvgMin(): ?float
    {
        return $this->weightAvgMin;
    }

    public function setWeightAvgMin(float $weightAvgMin): self
    {
        $this->weightAvgMin = $weightAvgMin;

        return $this;
    }

    public function getWeightAvgMax(): ?float
    {
        return $this->weightAvgMax;
    }

    public function setWeightAvgMax(float $weightAvgMax): self
    {
        $this->weightAvgMax = $weightAvgMax;

        return $this;
    }
}

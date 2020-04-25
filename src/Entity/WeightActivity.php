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
     * @ORM\Column(type="integer")
     */
    protected $weightAvgMin;

    /**
     * @ORM\Column(type="integer")
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

    public function getWeightAvgMin(): ?int
    {
        return $this->weightAvgMin;
    }

    public function setWeightAvgMin(int $weightAvgMin): self
    {
        $this->weightAvgMin = $weightAvgMin;

        return $this;
    }

    public function getWeightAvgMax(): ?int
    {
        return $this->weightAvgMax;
    }

    public function setWeightAvgMax(int $weightAvgMax): self
    {
        $this->weightAvgMax = $weightAvgMax;

        return $this;
    }
}

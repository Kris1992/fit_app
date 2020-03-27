<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BodyweightActivityRepository")
 */
class BodyweightActivity extends AbstractActivity
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
     * @ORM\Column(type="string", length=25)
     */
    protected $intensity;

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

    public function getIntensity(): ?string
    {
        return $this->intensity;
    }

    public function setIntensity(string $intensity): self
    {
        $this->intensity = $intensity;

        return $this;
    }


}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovementActivityRepository")
 */
class MovementActivity extends AbstractActivity
{

    /**
     * @ORM\Column(type="float")
     */
    protected $speedAverage;

    /**
     * @ORM\Column(type="string", length=25)
     */
    protected $intensity;

    public function getSpeedAverage(): ?float
    {
        return $this->speedAverage;
    }

    public function setSpeedAverage(float $speedAverage): self
    {
        $this->speedAverage = $speedAverage;

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

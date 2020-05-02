<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RouteDataRepository")
 */
class RouteData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $altitudeMax;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $altitudeMin;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $temperature;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $weatherConditions;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAltitudeMax(): ?float
    {
        return $this->altitudeMax;
    }

    public function setAltitudeMax(?float $altitudeMax): self
    {
        $this->altitudeMax = $altitudeMax;

        return $this;
    }

    public function getAltitudeMin(): ?float
    {
        return $this->altitudeMin;
    }

    public function setAltitudeMin(?float $altitudeMin): self
    {
        $this->altitudeMin = $altitudeMin;

        return $this;
    }

    public function getTemperature(): ?int
    {
        return $this->temperature;
    }

    public function setTemperature(?int $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getWeatherConditions(): ?string
    {
        return $this->weatherConditions;
    }

    public function setWeatherConditions(?string $weatherConditions): self
    {
        $this->weatherConditions = $weatherConditions;

        return $this;
    }
}

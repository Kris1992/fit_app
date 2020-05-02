<?php

namespace App\Form\Model\Workout;

class RouteDataModel
{

    private $id;

    private $altitudeMax;

    private $altitudeMin;

    private $temperature;

    private $weatherConditions;

    public function setId(?int $id)
    {
        $this->id = $id;

        return $this;
    }

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

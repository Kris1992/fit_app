<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovementSetRepository")
 */
class MovementSet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Workout", inversedBy="movementSets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $workout;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AbstractActivity")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activity;

    /**
     * @ORM\Column(type="float")
     */
    private $distance;

    /**
     * @ORM\Column(type="integer")
     */
    private $durationSeconds;

    /**
     * @ORM\Column(type="integer")
     */
    private $burnoutEnergy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkout(): ?Workout
    {
        return $this->workout;
    }

    public function setWorkout(?Workout $workout): self
    {
        $this->workout = $workout;

        return $this;
    }

    public function getActivity(): ?AbstractActivity
    {
        return $this->activity;
    }

    public function setActivity(?AbstractActivity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getDurationSeconds(): ?int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(int $durationSeconds): self
    {
        $this->durationSeconds = $durationSeconds;

        return $this;
    }

    public function getBurnoutEnergy(): ?int
    {
        return $this->burnoutEnergy;
    }

    public function setBurnoutEnergy(int $burnoutEnergy): self
    {
        $this->burnoutEnergy = $burnoutEnergy;

        return $this;
    }

}

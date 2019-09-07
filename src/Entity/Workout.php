<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

use App\Validator\Constraints as AcmeAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkoutRepository")
 */
class Workout
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("main")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="workouts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="workouts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"main", "input"})
     */
    private $activity;

    /**
     * @ORM\Column(type="time")
     * @AcmeAssert\NotZeroDuration()
     * @Groups({"main", "input"})
     */
    private $duration;

     /**
     * @ORM\Column(type="integer")
     * @Groups("main")
     */
    private $burnoutEnergy;


    /// Helper variables
    /**
    * @Groups("main")
    */
    private $time;
    /**
    * @Groups("main")
    */
    private $links = [];

    public function setTime(string $time): self
    {
        $this->time = $time;

        return $this;
    }
    public function getTime(): ?string
    {
        return $this->time;
    }
    public function setLinks(string $type,string $url): self
    {
        $this->links[$type] = $url;

        return $this;
    }
    public function getLinks(): ?array
    {
        return $this->links;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(\DateTimeInterface $duration): self
    {
        $this->duration = $duration;

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

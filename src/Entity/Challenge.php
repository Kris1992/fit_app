<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChallengeRepository")
 * @ORM\Table(name="challenge", indexes={@ORM\Index(columns={"title", "activity_name", "activity_type"}, flags={"fulltext"})})
 */
class Challenge
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $activityType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $activityName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $stopAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="challenges")
     */
    private $participants;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $goalProperty;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getActivityType(): ?string
    {
        return $this->activityType;
    }

    public function setActivityType(string $activityType): self
    {
        $this->activityType = $activityType;

        return $this;
    }

    public function getActivityName(): ?string
    {
        return $this->activityName;
    }

    public function setActivityName(string $activityName): self
    {
        $this->activityName = $activityName;

        return $this;
    }
    
    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
        }

        return $this;
    }

    /**
     * isParticipant Check is user participant
     * @param User $user User to check
     * @return bool
     */
    public function isParticipant(User $user): bool
    {
        return $this->participants->contains($user);
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function creationTimeStamp(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getStopAt(): ?\DateTimeInterface
    {
        return $this->stopAt;
    }

    public function setStopAt(\DateTimeInterface $stopAt): self
    {
        $this->stopAt = $stopAt;

        return $this;
    }

    public function getGoalProperty(): ?string
    {
        return $this->goalProperty;
    }

    public function setGoalProperty(string $goalProperty): self
    {
        $this->goalProperty = $goalProperty;

        return $this;
    }

    /**
     * getGoalPropertyDescription Get string with human readable description 
     * @return string|null
     */
    public function getGoalPropertyDescription(): string
    {
        switch ($this->goalProperty) {
            case 'durationSecondsTotal':
                return 'Most time spent';
            case 'burnoutEnergyTotal':
                return 'Most burnout calories';
            case 'distanceTotal':
                return 'Most distance traveled';
            case 'repetitionsTotal':
                return 'Most number of repetitions';
            default:
                return '';
        }
    }

}

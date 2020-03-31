<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\AbstractActivity", inversedBy="workouts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"main", "input"})
     */
    private $activity;

     /**
     * @ORM\Column(type="integer")
     * @Groups("main")
     */
    private $burnoutEnergyTotal;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("main")
     */
    private $startAt;
    
    /**
     * @ORM\Column(type="integer")
     * @AcmeAssert\NotZeroDuration()
     * @Groups({"main", "input"})
     */
    private $durationSecondsTotal;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("main")
     */
    private $distanceTotal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $repetitionsTotal;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MovementSet", mappedBy="workout", 
     * orphanRemoval=true, cascade={"persist", "refresh"})
     */
    private $movementSets;

    public function __construct()
    {
        $this->movementSets = new ArrayCollection();
    }



    /// Helper variables
    /**
    * @Groups("main")
    */
    private $time;
    /**
    * @Groups("main")
    */
    private $links = [];
    /**
    * @Groups("main")
    */
    private $startDate;




 
    /**
     * transformSaveTimeToString Transform time from seconds to string format H:i:s
     * @return self 
     */
    public function transformSaveTimeToString(): self
    {
        $seconds = $this->getDurationSecondsTotal();
        $array['hour'] = (int)($seconds / (60 * 60));
        $array['minute'] = (int)(($seconds / 60) % 60);
        $array['second'] = (int)($seconds % 60);

        foreach ($array as $key => $value) {
            $array[$key] = $this->transformToTimeString($value);
        }

        $format = '%s:%s:%s';
        $timeString = sprintf($format, $array['hour'], $array['minute'], $array['second']);

        $this->time = $timeString;

        return $this;
    }

    /**
     * transformToTimeString Transform time value(hour, minute etc.) to string format 01,11 etc.
     * @param  int    $value Time value to transform 
     * @return string
     */
    private function transformToTimeString(int $value):string
    {
        $value = str_pad((string)$value, 2, '0', STR_PAD_LEFT);

        return $value;
    }


    //setTime to delete
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

    public function setStartDate(string $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function setBurnoutEnergyTotal(int $burnoutEnergyTotal): self
    {
        $this->burnoutEnergyTotal = $burnoutEnergyTotal;

        return $this;   
    }



    // Used in Fixtures
    public function calculateSaveBurnoutEnergyTotal(): self
    {
        $activity = $this->activity;
        $activityEnergy = $activity->getEnergy();

        $workoutDurationTotal = $this->durationSecondsTotal;
        $burnoutEnergyTotal = $activityEnergy * ($workoutDurationTotal/(60*60));

        $this->burnoutEnergyTotal = $burnoutEnergyTotal;

        return $this;
    }

    public function calculateSaveDistanceTotal(): self
    { 
        $activity = $this->activity;
        $speedDiff = $activity->getSpeedAverageMax() - $activity->getSpeedAverageMin();
        $speed = $activity->getSpeedAverageMin() + ($speedDiff / 2);

        $distanceTotal = $speed * ($this->getDurationSecondsTotal() / 3600);

        $this->distanceTotal = $distanceTotal;

        return $this;

    }
    //end of fixtures

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getActivity(): ?AbstractActivity
    {
        return $this->activity;
    }

    public function setActivity(AbstractActivity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getBurnoutEnergyTotal(): ?int
    {
        return $this->burnoutEnergyTotal;
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

    public function getDurationSecondsTotal(): ?int
    {
        return $this->durationSecondsTotal;
    }

    public function setDurationSecondsTotal(int $durationSecondsTotal): self
    {
        $this->durationSecondsTotal = $durationSecondsTotal;

        return $this;
    }

    public function getDistanceTotal(): ?float
    {
        return $this->distanceTotal;
    }

    public function setDistanceTotal(?float $distanceTotal): self
    {
        $this->distanceTotal = $distanceTotal;

        return $this;
    }

    /**
     * @return Collection|MovementSet[]
     */
    public function getMovementSets(): Collection
    {
        return $this->movementSets;
    }

    public function addMovementSet(MovementSet $movementSet): self
    {
        if (!$this->movementSets->contains($movementSet)) {
            $this->movementSets[] = $movementSet;
            $movementSet->setWorkout($this);
        }

        return $this;
    }

    public function removeMovementSet(MovementSet $movementSet): self
    {
        if ($this->movementSets->contains($movementSet)) {
            $this->movementSets->removeElement($movementSet);
            // set the owning side to null (unless already changed)
            if ($movementSet->getWorkout() === $this) {
                $movementSet->setWorkout(null);
            }
        }

        return $this;
    }

    public function getRepetitionsTotal(): ?int
    {
        return $this->repetitionsTotal;
    }

    public function setRepetitionsTotal(?int $repetitionsTotal): self
    {
        $this->repetitionsTotal = $repetitionsTotal;

        return $this;
    }

}

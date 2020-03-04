<?php
namespace App\Form\Model\Workout;

use Symfony\Component\Validator\Constraints as Assert;

class WorkoutSpecificFormModel
{

    private $id;

    private $user;

    private $activity;

    private $burnoutEnergy;

    /**
     * @Assert\NotBlank(message="Please enter date of start")
     */
    private $startAt;
    
    /**
     * @Assert\NotBlank(message="Please enter time")
     */
    private $durationSeconds;

    /**
     * @Assert\NotBlank(message="Please enter distance")
     */
    private $distance;








    //helpers

    private $activityName;

    private $averageSpeed;

    private $averageCadence;



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

    public function getActivity(): ?AbstractActivity
    {
        return $this->activity;
    }

    public function setActivity(?AbstractActivity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function calculateSaveBurnoutEnergy(): self
    {
        $activity = $this->activity;
        $activityEnergy = $activity->getEnergy();

        $workoutDuration = $this->durationSeconds;
        $burnoutEnergy = $activityEnergy * ($workoutDuration/(60*60));

        $this->burnoutEnergy = $burnoutEnergy;

        return $this;
    }

    public function getBurnoutEnergy(): ?int
    {
        return $this->burnoutEnergy;
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

    public function getDurationSeconds(): ?int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(int $durationSeconds): self
    {
        $this->durationSeconds = $durationSeconds;

        return $this;
    }

    public function setActivityName(string $activityName): self
    {
        $this->activityName = $activityName;

        return $this;
    }

    public function getActivityName(): ?string
    {
        return $this->activityName;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function calculateSaveAverageSpeed(): self
    {
        $this->averageSpeed = $this->getDistance()/($this->getDurationSeconds()/3600);

        return $this;
    }

    public function getAverageSpeed(): ?float
    {
        return $this->averageSpeed;
    }
   
   


    //arrayAccess methods
    /*public function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }*/

}

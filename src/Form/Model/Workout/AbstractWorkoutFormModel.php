<?php
namespace App\Form\Model\Workout;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AcmeAssert;
use App\Entity\User;
use App\Entity\AbstractActivity;

abstract class AbstractWorkoutFormModel
{

    protected $id;

    /**
     * @Assert\NotBlank(message="Cannot configure user", groups={"model"})
     */
    protected $user;

    /**
     * @Assert\NotBlank(message="Cannot configure activity", groups={"model"})
     */
    protected $activity;

    /**
     * @Assert\NotBlank(message="Your burnout Energy is too small", groups={"model"})
     * @Assert\GreaterThan(message="Your burnout Energy is too small", value=0, groups={"model"})
     */
    protected $burnoutEnergyTotal;

    /**
     * @Assert\NotBlank(message="Please enter date of start", groups={"sets", "Default"})
     */
    protected $startAt;
    
    /**
     * @Assert\NotBlank(message="Please enter time", groups={"model", "Default"})
     * @AcmeAssert\NotZeroDuration(groups={"model", "Default"})
     */
    protected $durationSecondsTotal;


    //helpers

    protected $activityName;

    protected $type;

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
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

    public function getActivity(): ?AbstractActivity
    {
        return $this->activity;
    }

    public function setActivity(?AbstractActivity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function calculateSaveBurnoutEnergyTotal(): self
    {
        $activity = $this->activity;
        $activityEnergy = $activity->getEnergy();

        $workoutDurationTotal = $this->durationSecondsTotal;
        $burnoutEnergyTotal = $activityEnergy * ($workoutDurationTotal/(60*60));

        $this->burnoutEnergyTotal = $burnoutEnergyTotal;

        return $this;
    }

    public function setBurnoutEnergyTotal(?int $burnoutEnergyTotal): self
    {
        $this->burnoutEnergyTotal = $burnoutEnergyTotal;

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

    public function setActivityName(string $activityName): self
    {
        $this->activityName = $activityName;

        return $this;
    }

    public function getActivityName(): ?string
    {
        return $this->activityName;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

}

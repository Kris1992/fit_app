<?php
namespace App\Form\Model\Workout;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AcmeAssert;
use App\Services\ImagesManager\WorkoutsImagesManager;
use App\Entity\User;
use App\Entity\AbstractActivity;

abstract class AbstractWorkoutFormModel
{

    protected $id;

    /**
     * @Assert\NotBlank(message="Cannot configure user", groups={"model", "bodyweight_model"})
     */
    protected $user;

    /**
     * @Assert\NotBlank(message="Cannot configure activity", groups={"model", 
     * "bodyweight_model"})
     */
    protected $activity;

    /**
     * @Assert\NotBlank(message="Your burnout Energy is too small", groups={"model", 
     * "bodyweight_model"})
     * @Assert\GreaterThan(message="Your burnout Energy is too small", value=0, 
     * groups={"model", "bodyweight_model"})
     */
    protected $burnoutEnergyTotal;

    /**
     * @Assert\NotBlank(message="Please enter date of start", groups={"average_sets", 
     * "specific_sets", "Default", "route_map"})
     */
    protected $startAt;
    
    /**
     * @Assert\NotBlank(message="Please enter time", groups={"model", "bodyweight_model",
     *  "Default"})
     * @AcmeAssert\NotZeroDuration(groups={"model", "bodyweight_model", "Default", "route_map"})
     */
    protected $durationSecondsTotal;

    protected $imageFilename;

    //helpers
    /**
     * @Assert\NotBlank(message="Please enter activity name", groups={"specific_sets", "route_map"})
     * @Assert\NotNull(message="Please enter activity name", groups={"specific_sets", "route_map"})
     */
    protected $activityName;

    protected $type;



    public function setId(?int $id)
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

    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
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

    public function getImagePath(): ?string
    {
        return WorkoutsImagesManager::WORKOUTS_IMAGES.'/'.$this->getUser()->getLogin().'/'.$this->getImageFilename();
    }

    public function getThumbImagePath(): ?string
    {
        return WorkoutsImagesManager::WORKOUTS_IMAGES.'/'.$this->getUser()->getLogin().'/'.WorkoutsImagesManager::THUMB_IMAGES.'/'.$this->getImageFilename();
    }


}

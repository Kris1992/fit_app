<?php
declare(strict_types=1);

namespace App\Form\Model\Workout;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AcmeAssert;
use App\Services\ImagesManager\ImagesConstants;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;
use App\Entity\User;
use App\Entity\AbstractActivity;

abstract class AbstractWorkoutFormModel
{

    protected $id;

    /**
     * @Assert\NotBlank(message="Cannot configure user", groups={"model", "bodyweight_model", 
     * "route_model", "weight_model"})
     */
    protected $user;

    /**
     * @Assert\NotBlank(message="Cannot configure activity", groups={"model", 
     * "bodyweight_model", "weight_model", "route_model"})
     */
    protected $activity;

    /**
     * @Assert\NotBlank(message="Your burnout Energy is too small", groups={"model", 
     * "bodyweight_model", "route_model", "weight_model"})
     * @Assert\GreaterThan(message="Your burnout Energy is too small", value=0, 
     * groups={"model", "bodyweight_model", "route_model", "weight_model"})
     */
    protected $burnoutEnergyTotal;

    /**
     * @Assert\NotBlank(message="Please enter date of start", groups={"average_sets", 
     * "specific_sets", "Default", "route_map", "route_model", "weight_model"})
     */
    protected $startAt;
    
    /**
     * @Assert\NotBlank(message="Please enter time", groups={"model", "bodyweight_model",
     *  "Default", "route_model", "weight_model"})
     * @AcmeAssert\NotZeroDuration(groups={"model", "bodyweight_model", "Default", "route_map", "route_model", "weight_model"})
     */
    protected $durationSecondsTotal;

    /**
     *  @Assert\NotBlank(message="Map missing", groups={"route_model"})
     */
    protected $imageFilename;

    /**
     * @Assert\Valid
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "You must specify at least one set",
     *      groups={"specific_sets", "average_sets"}
     * )
     */
    protected $movementSets;

    protected $routeData;

    //helpers
    /**
     * @Assert\NotBlank(message="Please enter activity name", groups={"specific_sets", "route_map"})
     * @Assert\NotNull(message="Please enter activity name", groups={"specific_sets", "route_map"})
     */
    protected $activityName;

    protected $type;


    public function __construct()
    {
        $this->movementSets = new ArrayCollection();
    }

    /**
     * @return Collection|MovementActivitySetFormModel[]
     */
    public function getMovementSets(): Collection
    {
        return $this->movementSets;
    }

    public function addMovementSet(MovementActivitySetFormModel $movementSet): self
    {
        if (!$this->movementSets->contains($movementSet)) {
            $this->movementSets[] = $movementSet;
            $movementSet->setWorkout($this);
        }

        return $this;
    }

    public function removeMovementSet(MovementActivitySetFormModel $movementSet): self
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

        $this->burnoutEnergyTotal = intval($burnoutEnergyTotal);

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

    public function getRouteData(): ?RouteDataModel
    {
        return $this->routeData;
    }

    public function setRouteData(?RouteDataModel $routeData): self
    {
        $this->routeData = $routeData;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return ImagesConstants::WORKOUTS_IMAGES.'/'.$this->getUser()->getLogin().'/'.$this->getImageFilename();
    }

    public function getThumbImagePath(): ?string
    {
        return ImagesConstants::WORKOUTS_IMAGES.'/'.$this->getUser()->getLogin().'/'.ImagesConstants::THUMB_IMAGES.'/'.$this->getImageFilename();
    }

}

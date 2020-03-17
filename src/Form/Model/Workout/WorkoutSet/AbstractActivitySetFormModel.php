<?php
namespace App\Form\Model\Workout\WorkoutSet;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AcmeAssert;

use App\Entity\AbstractActivity;
use App\Form\Model\Workout\AbstractWorkoutFormModel;

/**
 * Abstract class of activity set model to WorkoutModels
 */
class AbstractActivitySetFormModel
{

    protected $id;

    /**
     * @Assert\NotBlank(message="Cannot configure workout", groups={"model"})
     */
    protected $workout;

    /**
     * @Assert\NotBlank(message="Choose activity", groups={"average_sets", "model"})
     */
    protected $activity;

    /**
     * @Assert\NotBlank(message="Please enter time", groups={"average_sets", "specific_sets"})
     * @AcmeAssert\NotZeroDuration(groups={"average_sets", "specific_sets"})
     */
    protected $durationSeconds;

    /**
     * @Assert\NotBlank(message="Your set burnout Energy is too small", groups={"model"})
     * @Assert\GreaterThan(message="Your set burnout Energy is too small", value=0, 
     * groups={"model"})
     */
    protected $burnoutEnergy;

    //Helpers properties
    protected $activityName;

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkout(): ?AbstractWorkoutFormModel
    {
        return $this->workout;
    }

    public function setWorkout(?AbstractWorkoutFormModel $workout): self
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

    public function getDurationSeconds(): ?int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(?int $durationSeconds): self
    {
        $this->durationSeconds = $durationSeconds;

        return $this;
    }

    public function getBurnoutEnergy(): ?int
    {
        return $this->burnoutEnergy;
    }

    public function setBurnoutEnergy(?int $burnoutEnergy): self
    {
        $this->burnoutEnergy = $burnoutEnergy;

        return $this;
    }

    public function calculateSaveBurnoutEnergy()
    {
        $activity = $this->activity;
        $activityEnergy = $activity->getEnergy();

        $workoutDuration = $this->durationSeconds;
        $burnoutEnergy = $activityEnergy * ($workoutDuration/(60*60));

        $this->burnoutEnergy = $burnoutEnergy;

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
}

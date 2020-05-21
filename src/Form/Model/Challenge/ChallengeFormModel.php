<?php

namespace App\Form\Model\Challenge;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

class ChallengeFormModel
{

    private $id;

    /**
     * @Assert\NotBlank(message="Please enter title.") 
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Title cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */ 
    private $title;

    /**
     * @Assert\NotBlank(message="Please choose activity type.")
     */
    private $activityType;

    /**
     * @Assert\NotBlank(message="Please choose activity name.")
     */
    private $activityName;

    /**
     * @Assert\NotBlank(message="Please choose goal.")
     */
    private $goalProperty;

    /**
     * @Assert\DateTime
     * @Assert\GreaterThan("today UTC")
     */
    private $startAt;

    /**
     * @Assert\DateTime
     * @Assert\GreaterThan(
     *     propertyPath="startAt",
     *     message="Date of end of the challenge must be graeter than start."
     *     )
     */
    private $stopAt;

    private $createdAt;

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

   public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getActivityType(): ?string
    {
        return $this->activityType;
    }

    public function setActivityType(?string $activityType): self
    {
        $this->activityType = $activityType;

        return $this;
    }

    public function getActivityName(): ?string
    {
        return $this->activityName;
    }

    public function setActivityName(?string $activityName): self
    {
        $this->activityName = $activityName;

        return $this;
    }

    public function getGoalProperty(): ?string
    {
        return $this->goalProperty;
    }

    public function setGoalProperty(?string $goalProperty): self
    {
        $this->goalProperty = $goalProperty;

        return $this;
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
}

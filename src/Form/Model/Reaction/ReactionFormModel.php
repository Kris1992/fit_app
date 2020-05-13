<?php

namespace App\Form\Model\Reaction;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use App\Entity\Workout;

class ReactionFormModel
{

    private $id;

    /**
     * @Assert\NotBlank(message="Cannot add reaction")
     */
    private $owner;

    /**
     * @Assert\NotBlank(message="Cannot add reaction")
     */
    private $workout;

    /**
     * @Assert\NotBlank(message="Cannot add reaction")
     * @Assert\Range(
     *      min = 0,
     *      max = 2,
     *      minMessage = "Cannot add this reaction",
     *      maxMessage = "Cannot add this reaction"
     * )
     */
    private $type;

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }
}

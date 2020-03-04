<?php
namespace App\Form\Model\Activity;

use Symfony\Component\Validator\Constraints as Assert;

class WeightActivityFormModel extends AbstractActivityFormModel
{
	/**
     * @Assert\NotBlank(message="Please enter number of repetitions")
     */
    private $repetitions;

    /**
     * @Assert\NotBlank(message="Please enter weight")
     * @Assert\Range(
     *      min = 1,
     *      max = 200,
     *      minMessage = "Your load must be at least {{ limit }}kg",
     *      maxMessage = "Your load cannot be larger than {{ limit }}kg"
     * )
     */
    private $weight;// change to load in future (now it is only for check it all works fine)

    public function getRepetitions(): ?int
    {
        return $this->repetitions;
    }

    public function setRepetitions(int $repetitions): self
    {
        $this->repetitions = $repetitions;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        
        $this->weight = $weight;

        return $this;
    }
}

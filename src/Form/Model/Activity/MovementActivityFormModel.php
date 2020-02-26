<?php
//TO DELETE
namespace App\Form\Model\Activity;

use Symfony\Component\Validator\Constraints as Assert;

class MovementActivityFormModel extends AbstractActivityFormModel
{
	/**
     * @Assert\NotBlank(message="Please enter average speed")
     * // w przedziale 0-20
     */
    private $speedAverage;

    public function getSpeedAverage(): ?float
    {
        return $this->speedAverage;
    }

    public function setSpeed(float $speedAverage): self
    {
        $this->speedAverage = $speedAverage;

        return $this;
    }
}

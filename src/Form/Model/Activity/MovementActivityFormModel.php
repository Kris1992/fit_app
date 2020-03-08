<?php
namespace App\Form\Model\Activity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\UniqueRangeSpeed;
use App\Validator\UniqueFieldsPair;


/**
* @UniqueRangeSpeed(
*     fields={"speedAverageMin", "speedAverageMax"},
*     errorPath="name",
*     entityClass="MovementActivity"
*)
*
* @UniqueFieldsPair(
*     fields={"intensity", "name"},
*     errorPath="name",
*     entityClass="MovementActivity",
*     message="The activity with the same name and intensity already exist"
* )
*/
class MovementActivityFormModel extends AbstractActivityFormModel
{

	/**
     * @Assert\NotBlank(message="Please enter average speed")
     * @Assert\Range(
     *      min = 1,
     *      max = 20,
     *      minMessage = "Lowest average speed must be at least {{ limit }}km/h",
     *      maxMessage = "Lowest average speed cannot be greater than {{ limit }}km/h"
     * )
     */
    private $speedAverageMin;

    /**
     * @Assert\NotBlank(message="Please enter average speed")
     * @Assert\Range(
     *      min = 5,
     *      max = 75,
     *      minMessage = "Largest average speed must be at least {{ limit }}km/h",
     *      maxMessage = "Largest average speed cannot be greater than {{ limit }}km/h"
     * )
     * @Assert\GreaterThan(
     *     propertyPath="speedAverageMin",
     *     message="Largest average speed must be greater than lowest"
     *     )
     */
    private $speedAverageMax;

     /**
     * @Assert\NotBlank(message="Please enter intensity")
     */
    private $intensity;

    public function getSpeedAverageMin(): ?float
    {
        return $this->speedAverageMin;
    }

    public function setSpeedAverageMin(?float $speedAverageMin): self
    {
        $this->speedAverageMin = $speedAverageMin;

        return $this;
    }

    public function getSpeedAverageMax(): ?float
    {
        return $this->speedAverageMax;
    }

    public function setSpeedAverageMax(?float $speedAverageMax): self
    {
        $this->speedAverageMax = $speedAverageMax;

        return $this;
    }

    public function getIntensity(): ?string
    {
        return $this->intensity;
    }

    public function setIntensity(?string $intensity): self
    {
        $this->intensity = $intensity;

        return $this;
    }
}

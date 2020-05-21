<?php
declare(strict_types=1);

namespace App\Form\Model\Activity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\UniqueFieldsPair;
use App\Validator\UniqueRangeProperties;

/**
*
*@UniqueRangeProperties(
*     fields={"repetitionsAvgMin", "repetitionsAvgMax"},
*     errorPath="name",
*     entityClass="BodyweightActivity"
*)
* @UniqueFieldsPair(
*     fields={"intensity", "name"},
*     errorPath="name",
*     entityClass="BodyweightActivity",
*     message="The activity with the same name and intensity already exist"
* )
*/
class BodyweightActivityFormModel extends AbstractActivityFormModel
{

    /**
     * @Assert\NotBlank(message="Please enter number of repetitions")
     * @Assert\Range(
     *      min = 1,
     *      max = 200,
     *      minMessage = "Lowest average number of repetitions must be at least {{ limit }}",
     *      maxMessage = "Lowest average number of repetitions cannot be greater than {{ limit 
     *      }}"
     * )
     */
    private $repetitionsAvgMin;

    /**
     * @Assert\NotBlank(message="Please enter number of repetitions")
     * @Assert\Range(
     *      min = 2,
     *      max = 500,
     *      minMessage = "Largest average number of repetitions must be at least {{ limit }}",
     *      maxMessage = "Largest average number of repetitions cannot be greater than {{ limit 
     *      }}"
     * )
     * @Assert\GreaterThan(
     *     propertyPath="repetitionsAvgMin",
     *     message="Largest average number of repetitions must be greater than lowest"
     *     )
     */
    private $repetitionsAvgMax;
    
    /**
     * @Assert\NotBlank(message="Please enter intensity")
     * @Assert\Choice(callback="getAvaibleIntensities", message="Choose a valid intensity.")
     */
    private $intensity;

    public function getRepetitionsAvgMin(): ?int
    {
        return $this->repetitionsAvgMin;
    }

    public function setRepetitionsAvgMin(?int $repetitionsAvgMin): self
    {
        $this->repetitionsAvgMin = $repetitionsAvgMin;

        return $this;
    }

    public function getRepetitionsAvgMax(): ?int
    {
        return $this->repetitionsAvgMax;
    }

    public function setRepetitionsAvgMax(?int $repetitionsAvgMax): self
    {
        $this->repetitionsAvgMax = $repetitionsAvgMax;

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

    public static function getAvaibleIntensities(): array
    {
        $intensities = ['Very low', 'Low', 'Normal', 'High', 'Very high'];
        return array_combine($intensities, $intensities);
    }
}

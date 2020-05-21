<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovementActivityRepository")
 */
class MovementActivity extends AbstractActivity
{

    /**
     * @ORM\Column(type="float")
     * @Groups({"main"})
     */
    protected $speedAverageMin;

    /**
     * @ORM\Column(type="float")
     * @Groups({"main"})
     */
    protected $speedAverageMax;

    /**
     * @ORM\Column(type="string", length=25)
     */
    protected $intensity;

    public function getSpeedAverageMin(): ?float
    {
        return $this->speedAverageMin;
    }

    public function setSpeedAverageMin(float $speedAverageMin): self
    {
        $this->speedAverageMin = $speedAverageMin;

        return $this;
    }

    public function getSpeedAverageMax(): ?float
    {
        return $this->speedAverageMax;
    }

    public function setSpeedAverageMax(float $speedAverageMax): self
    {
        $this->speedAverageMax = $speedAverageMax;

        return $this;
    }

    public function getIntensity(): ?string
    {
        return $this->intensity;
    }

    public function setIntensity(string $intensity): self
    {
        $this->intensity = $intensity;

        return $this;
    }
}

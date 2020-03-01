<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

 /**
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminator")
 * @ORM\Entity(repositoryClass="App\Repository\AbstractActivityRepository")
 *
 * @ORM\Table(name="abstract_activity", indexes={@ORM\Index(columns={"name", "type"}, 
 * flags={"fulltext"})})
 */
abstract class AbstractActivity implements \ArrayAccess
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"main"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"main", "input"})
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"main"})
     */
    protected $energy;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Workout", mappedBy="activity", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $workouts;


    public function __construct()
    {
        $this->workouts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEnergy(): ?int
    {
        return $this->energy;
    }

    public function setEnergy(int $energy): self
    {
        $this->energy = $energy;

        return $this;
    }

    /**
     * @return Collection|Workout[]
     */
    public function getWorkouts(): Collection
    {
        return $this->workouts;
    }

    public function addWorkout(Workout $workout): self
    {
        if (!$this->workouts->contains($workout)) {
            $this->workouts[] = $workout;
            $workout->setActivity($this);
        }

        return $this;
    }

    public function removeWorkout(Workout $workout): self
    {
        if ($this->workouts->contains($workout)) {
            $this->workouts->removeElement($workout);
            // set the owning side to null (unless already changed)
            if ($workout->getActivity() === $this) {
                $workout->setActivity(null);
            }
        }

        return $this;
    }

    //arrayAccess methods
    public function offsetExists($offset)
    {
        /* 
        $value = $this->{"get$offset"}();
        return $value !== null;
        */
        return isset($this->$offset);
    }

    public function offsetGet($offset)
    {
        return $this->{"get$offset"}();
    }

    public function offsetSet($offset, $value)
    {
        $this->{"set$offset"}($value);
    }

    public function offsetUnset($offset)
    {
        $this->{"set$offset"}(null);
    }
}

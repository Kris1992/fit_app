<?php
//TO DELETE
namespace App\Form\Model\Activity;

use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractActivityFormModel implements \ArrayAccess
{

    protected $id;

    /**
     * @Assert\NotBlank(message="Please enter type")
     */
    protected $type;

    //unique name
    /**
     * @Assert\NotBlank(message="Please enter name")
     */
    protected $name;

    /**
     * @Assert\NotBlank(message="Please enter energy")
     */
    protected $energy;



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



    //arrayAccess methods
    public function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

}

<?php
namespace App\Form\Model\Activity;

use Symfony\Component\Validator\Constraints as Assert;

class BasicActivityFormModel implements \ArrayAccess
{

	/**
     * @Assert\NotBlank(message="Please enter type")
     */
    protected $type;

    public function getId()
    {
        /* 
        return null because that model is used only for create and we need getId() function to 
        check is edit
        */
        return null;
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

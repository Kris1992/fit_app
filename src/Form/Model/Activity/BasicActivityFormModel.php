<?php
declare(strict_types=1);

namespace App\Form\Model\Activity;

use Symfony\Component\Validator\Constraints as Assert;

class BasicActivityFormModel implements \ArrayAccess
{

	/**
     * @Assert\NotBlank(message="Please enter type")
     */
    protected $type;

    protected $id;

    public function getId(): ?int
    {
        //just for bind form option isEdit
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

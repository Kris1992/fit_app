<?php

namespace App\Form\Model\Activity;

use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractActivityFormModel // implements \ArrayAccess //
{

    protected $id;

    /**
     * @Assert\NotBlank(message="Please enter type")
     */
    protected $type;

    /**
     * @Assert\NotBlank(message="Please enter name")
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your name cannot contain a number"
     * )
     */
    protected $name;

    /**
     * @Assert\NotBlank(message="Please enter energy")
     * @Assert\Positive()
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
    /*public function offsetExists($offset)
    {
        $value = $this->{"get$offset"}();
        return $value !== null;
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
    }*/

}

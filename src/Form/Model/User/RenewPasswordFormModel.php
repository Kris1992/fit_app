<?php

namespace App\Form\Model\User;

use Symfony\Component\Validator\Constraints as Assert;

use App\Validator\ContainsAlphanumeric;


class RenewPasswordFormModel
{

    /**
     * @Assert\NotBlank(message="Choose a password!")
     * @ContainsAlphanumeric()
     */
    private $plainPassword;
   
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }


}

<?php

namespace App\Form\Model\User;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\UniqueUser;
use App\Validator\ContainsAlphanumeric;
use App\Services\ImagesManager\ImagesManager;

/**
* @UniqueUser(
*     field="email",
*     errorPath="email"
*)
*/
class UserRegistrationFormModel
{
    
    private $id;

	/**
     * @Assert\NotBlank(message="Please enter an email")
     * @Assert\Email()
     */
    private $email;

     /**
     * @Assert\NotBlank(message="Please enter your first name!")
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your name cannot contain a number"
     * )
     */
    private $firstName;

    /**
     * @Assert\NotBlank(message="Please enter your second name!")
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your second name cannot contain a number"
     * )
     */
    private $secondName;

    /**
     * @Assert\NotBlank(message="Choose a password!", groups={"registration"})
     * @ContainsAlphanumeric()
     */
    private $plainPassword;

    private $role;

    /**
     * @Assert\NotNull(message="Choose a gender!")
     */
    private $gender;

    /**
     * @Assert\IsTrue(message="You must agree to our terms.")
     */
    private $agreeTerms;

    private $imageFilename;

    //additional options


    private $birthdate;

    /**
     * @Assert\Range(
     *      min = 30,
     *      max = 200,
     *      minMessage = "The minimal value is set to: {{ limit }} kg",
     *      maxMessage = "The maximal value is set to: {{ limit }} kg"
     * )
     */
    private $weight;

     /**
     * @Assert\Range(
     *      min = 120,
     *      max = 220,
     *      minMessage = "You must be at least {{ limit }} cm tall to enter",
     *      maxMessage = "You cannot be taller than {{ limit }} cm to enter"
     * )
     */
    private $height;


    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
    
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getSecondName(): ?string
    {
        return $this->secondName;
    }

    public function setSecondName(?string $secondName): self
    {
        $this->secondName = $secondName;

        return $this;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    //additional options
    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }
    
    public function getWeight(): ?int
    {
        return $this->weight;
    }
    
    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }
    
    public function getHeight(): ?int
    {
        return $this->height;
    }
    
    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getAgreeTerms(): ?bool
    {
        return $this->agreeTerms;
    }
    
    public function setAgreeTerms(?bool $agreeTerms): self
    {
        $this->agreeTerms = $agreeTerms;

        return $this;
    }

    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function getImagePath(): ?string
    {
        return ImagesManager::USERS_IMAGES.'/'.$this->getImageFilename();
    }

    public function getThumbImagePath(): ?string
    {
        return ImagesManager::USERS_IMAGES.'/'.ImagesManager::THUMB_IMAGES.'/'.$this->getImageFilename();
    }

}

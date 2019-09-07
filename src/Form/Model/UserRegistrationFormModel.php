<?php
namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use App\Validator\UniqueUser;
use App\Validator\ContainsAlphanumeric;

use App\Services\UploadImagesHelper;

/**
* @UniqueUser(
* fields={"email"},
* errorPath="email"
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
     */
    private $firstName;

    /**
     * @Assert\NotBlank(message="Please enter your second name!")
     */
    private $secondName;

    /**
     * @Assert\NotBlank(message="Choose a password!", groups={"registration"})
     * @ContainsAlphanumeric()
     */
    private $plainPassword;

    /**
     * @Assert\IsTrue(message="You must agree to our terms.")
     */
    private $agreeTerms;

    private $imageFilename;



    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function setPlainPassword(string $plainPassword): self
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

    public function setSecondName(string $secondName): self
    {
        $this->secondName = $secondName;

        return $this;
    }

    public function getAgreeTerms(): ?bool
    {
        return $this->agreeTerms;
    }
    public function setAgreeTerms(bool $agreeTerms): self
    {
        $this->agreeTerms = $agreeTerms;

        return $this;
    }

    public function setImageFilename(string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function getImagePath()
    {

        return UploadImagesHelper::USERS_IMAGES.'/'.$this->getImageFilename();// już teraz bez uploads/ ze względu na to że możemy potem przenieść pliki do innego folderu lub na chmurę
        //return 'uploads/article_image/'.$this->getImageFilename();
        //return 'images/'.$this->getImageFilename(); to było tymczasowe dla fakera
    }

}

?>
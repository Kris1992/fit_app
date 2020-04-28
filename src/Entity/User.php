<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Services\ImagesManager\ImagesConstants;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message= "This e-mail address is already registered!"
 * )
 * @ORM\Table(name="user", indexes={@ORM\Index(columns={"first_name", "second_name"}, 
 * flags={"fulltext"})})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups("main")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $login;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $secondName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $agreedTermsAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\PasswordToken", inversedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $passwordToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Workout", mappedBy="user", orphanRemoval=true)
     */
    private $workouts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageFilename;

    /**
     * @ORM\Column(type="integer")
     */
    private $failed_attempts = 0;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $gender;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $weight;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $height;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Curiosity", mappedBy="author", orphanRemoval=true)
     */
    private $curiosities;

    public function __construct()
    {
        $this->workouts = new ArrayCollection();
        $this->curiosities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function saveLogin(): self
    {
        $login = $this->getFirstName().'_'.$this->getSecondName().'-'.uniqid(); 
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->roles[0];
    }

    public function isAdmin(): bool
    {
        if ($this->getRole() === 'ROLE_ADMIN') {
            return true;
        }
        return false;
    } 

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        // not needed for apps that do not check user passwords
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function getAgreedTermsAt(): ?\DateTimeInterface
    {
        return $this->agreedTermsAt;
    }

   /* public function setAgreedTermsAt(\DateTimeInterface $agreedTermsAt): self
    {
        $this->agreedTermsAt = $agreedTermsAt;

        return $this;
    }*/
    public function agreeToTerms()
    {
        $this->agreedTermsAt = new \DateTime();
    }

    public function getPasswordToken(): ?PasswordToken
    {
        return $this->passwordToken;
    }

    public function setPasswordToken(?PasswordToken $passwordToken): self
    {
        $this->passwordToken = $passwordToken;

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
            $workout->setUser($this);
        }

        return $this;
    }

    public function removeWorkout(Workout $workout): self
    {
        if ($this->workouts->contains($workout)) {
            $this->workouts->removeElement($workout);
            // set the owning side to null (unless already changed)
            if ($workout->getUser() === $this) {
                $workout->setUser(null);
            }
        }

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getImagePath()
    {
        return ImagesConstants::USERS_IMAGES.'/'.$this->getLogin().'/'.$this->getImageFilename();
    }

    public function getThumbImagePath()
    {
        return ImagesConstants::USERS_IMAGES.'/'.$this->getLogin().'/'.ImagesConstants::THUMB_IMAGES.'/'.$this->getImageFilename();
    }

    public function increaseFailedAttempts(): ?int
    {
        $this->failed_attempts = $this->failed_attempts+1;
        return $this->failed_attempts;
    }

    public function getFailedAttempts(): ?int
    {
        return $this->failed_attempts;
    }

    public function resetFailedAttempts(): self
    {
        $this->failed_attempts = 0;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

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

    /**
     * @return Collection|Curiosity[]
     */
    public function getCuriosities(): Collection
    {
        return $this->curiosities;
    }

    public function addCuriosity(Curiosity $curiosity): self
    {
        if (!$this->curiosities->contains($curiosity)) {
            $this->curiosities[] = $curiosity;
            $curiosity->setAuthor($this);
        }

        return $this;
    }

    public function removeCuriosity(Curiosity $curiosity): self
    {
        if ($this->curiosities->contains($curiosity)) {
            $this->curiosities->removeElement($curiosity);
            // set the owning side to null (unless already changed)
            if ($curiosity->getAuthor() === $this) {
                $curiosity->setAuthor(null);
            }
        }

        return $this;
    }

}

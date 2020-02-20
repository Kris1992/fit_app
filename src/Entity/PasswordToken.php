<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PasswordTokenRepository")
 */
class PasswordToken
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=188, unique=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiredAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="passwordToken", cascade={"persist"})
     */
    private $user;


     public function __construct(User $user)
    {
        $this->token = bin2hex(random_bytes(60));
        $this->user = $user;
        $this->expiredAt = new \DateTime('+1 day');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(): self
    {
        $this->expiredAt = new \DateTime('+1 day');

        return $this;
    }
    //
    public function isExpired(): bool
    {
        return $this->getExpiredAt() <= new \DateTime();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        // set (or unset) the owning side of the relation if necessary
        $newPasswordToken = $user === null ? null : $this;
        if ($newPasswordToken !== $user->getPasswordToken()) {
            $user->setPasswordToken($newPasswordToken);
        }

        return $this;
    }
}

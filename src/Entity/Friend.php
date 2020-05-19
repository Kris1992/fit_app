<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FriendRepository")
 */
class Friend
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="invitedFriends")
     * @ORM\JoinColumn(nullable=false)
     */
    private $inviter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="invitedByFriends")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invitee;

    /**
     * @ORM\Column(type="string", length=10) //There is no enum type in sqlite (test env), columnDefinition="enum('Accepted', 'Rejected', 'Pending')")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInviter(): ?User
    {
        return $this->inviter;
    }

    public function setInviter(?User $inviter): self
    {
        $this->inviter = $inviter;

        return $this;
    }

    public function getInvitee(): ?User
    {
        return $this->invitee;
    }

    public function setInvitee(?User $invitee): self
    {
        $this->invitee = $invitee;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreated(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

}

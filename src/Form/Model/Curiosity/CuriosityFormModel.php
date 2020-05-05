<?php

namespace App\Form\Model\Curiosity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use App\Services\ImagesManager\ImagesConstants;

class CuriosityFormModel
{

    private $id;

    /*
        There is no option to change it we dont need assets here
    */
    private $author;

    /**
     * @Assert\NotBlank(message="Please enter title.") 
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */ 
    private $title;

    /**
     * @Assert\NotBlank(message="Please enter few words of description.")
     * @Assert\Length(
     *      max = 40,
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */
    private $description;

    /**
     *  //Assert\NotBlank(message="Curiosity must have image.")
     */
    private $mainImageFilename;

    /**
     * @Assert\NotBlank(message="Please enter content.")
     */
    private $content;

    private $isPublished;

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMainImageFilename(): ?string
    {
        return $this->mainImageFilename;
    }

    public function setMainImageFilename(?string $mainImageFilename): self
    {
        $this->mainImageFilename = $mainImageFilename;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }
    
    public function setIsPublished(?bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getImagePath()
    {
        return ImagesConstants::CURIOSITIES_IMAGES.'/'.$this->getAuthor()->getLogin().'/'.$this->getMainImageFilename();
    }

    public function getThumbImagePath()
    {
        return ImagesConstants::CURIOSITIES_IMAGES.'/'.$this->getAuthor()->getLogin().'/'.ImagesConstants::THUMB_IMAGES.'/'.$this->getMainImageFilename();
    }

}

<?php
declare(strict_types=1);

namespace App\Form\Model\Attachment;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Curiosity;

class AttachmentFormModel
{

    private $id;

    /**
     * @Assert\NotBlank(message="Filename cannot be blank.") 
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Filename cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */ 
    private $filename;

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

}

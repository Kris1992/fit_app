<?php
declare(strict_types=1);

namespace App\Form\Model\CSVFile;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

class CSVFileFormModel
{
    
    /**
     * @Assert\NotBlank(message="Please select file to upload")
     * @Assert\File(
     *      maxSize = "2M",
     *      mimeTypes = {"text/csv", "application/csv", "text/plain"},
     *      mimeTypesMessage = "Please upload a valid CSV file"
     *      
     * )
     */
    private $uploadedFile;

    public function setUploadedFile(?File $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
        
        return $this;
    }

    public function getUploadedFile(): ?File
    {
        return $this->uploadedFile;
    }

}

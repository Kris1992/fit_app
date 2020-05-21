<?php
declare(strict_types=1);

namespace App\Message\Event;

class UserImageFilenameDeletedEvent
{
    private $filename;
    private $subdirectory;

    public function __construct(string $filename, string $subdirectory)
    {
        $this->filename = $filename;
        $this->subdirectory = $subdirectory;
    }
    
    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getSubdirectory(): string
    {
        return $this->subdirectory;
    }


}
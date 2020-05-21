<?php
declare(strict_types=1);

namespace App\Message\Event;

class AttachmentDeletedEvent
{
    private $subdirectory;
    private $filename;

    public function __construct(string $subdirectory, string $filename)
    {
        $this->subdirectory = $subdirectory;
        $this->filename = $filename;
    }

    public function getSubdirectory(): string
    {
        return $this->subdirectory;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
    
}

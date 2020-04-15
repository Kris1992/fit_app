<?php

namespace App\Message\Command;

class DeleteWorkoutImage
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
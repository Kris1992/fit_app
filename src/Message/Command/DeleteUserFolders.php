<?php

namespace App\Message\Command;

class DeleteUserFolders
{
    private $subdirectory;

    public function __construct(string $subdirectory)
    {
        $this->subdirectory = $subdirectory;
    }

    public function getSubdirectory(): string
    {
        return $this->subdirectory;
    }
    
}
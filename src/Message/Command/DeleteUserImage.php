<?php

namespace App\Message\Command;

class DeleteUserImage
{
    private $userId;
    private $filename;
    private $subdirectory;

    public function __construct(int $userId, ?string $filename, ?string $subdirectory)
    {
        $this->userId = $userId;
        $this->filename = $filename;
        $this->subdirectory = $subdirectory;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getSubdirectory(): ?string
    {
        return $this->subdirectory;
    }
    
}
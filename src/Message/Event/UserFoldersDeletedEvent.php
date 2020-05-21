<?php
declare(strict_types=1);

namespace App\Message\Event;

class UserFoldersDeletedEvent
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
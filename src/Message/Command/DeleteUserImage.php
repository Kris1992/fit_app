<?php
declare(strict_types=1);

namespace App\Message\Command;

class DeleteUserImage
{
    private $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
    
}
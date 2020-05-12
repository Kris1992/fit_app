<?php

namespace App\Message\Command;

class CheckIsAttachmentUsed
{

    private $id;
    private $subdirectory;

    public function __construct(int $id, string $subdirectory)
    {
        $this->id = $id;
        $this->subdirectory = $subdirectory;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubdirectory(): string
    {
        return $this->subdirectory;
    }

}
<?php

namespace App\MessageHandler\Event;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Message\Event\UserImageFilenameDeletedEvent;
use App\Services\ImagesManager\ImagesManagerInterface;

class RemoveUserImageFileWhenImageFilenameDeleted implements MessageHandlerInterface
{
    private $imagesManager;

    public function __construct(ImagesManagerInterface $imagesManager)
    {
        $this->imagesManager = $imagesManager;
    }

    public function __invoke(UserImageFilenameDeletedEvent $event)
    {
        $isDeleted = $this->imagesManager->deleteImage($event->getFilename(), $event->getSubdirectory());
        if(!$isDeleted) {
            throw new \Exception("For some reason ImagesManager didn't delete user image");   
        }
    }
}
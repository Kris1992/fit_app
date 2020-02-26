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
        $this->imagesManager->deleteUserImage($event->getFilename());
    }
}
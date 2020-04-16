<?php

namespace App\MessageHandler\Event;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Message\Event\UserFoldersDeletedEvent;
use App\Services\FoldersManager\FoldersManagerInterface;
use App\Services\ImagesManager\ImagesManagerInterface;

class RemoveUserFoldersWhenUserIsDeleted implements MessageHandlerInterface
{
    private $foldersManager;
    private $userImagesManager;
    private $workoutsImagesManager;
    private $uploadsDirectory;

    public function __construct(FoldersManagerInterface $foldersManager, ImagesManagerInterface $userImagesManager, ImagesManagerInterface $workoutsImagesManager, string $uploadsDirectory)
    {
        $this->foldersManager = $foldersManager;
        $this->userImagesManager = $userImagesManager;
        $this->workoutsImagesManager = $workoutsImagesManager;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    public function __invoke(UserFoldersDeletedEvent $event)
    {
        $userImagesPath = $this->uploadsDirectory.'/'.$this->userImagesManager::USERS_IMAGES.'/'.$event->getSubdirectory();
        $workoutsImagesPath = $this->uploadsDirectory.'/'.$this->workoutsImagesManager::WORKOUTS_IMAGES.'/'.$event->getSubdirectory();

        $this->foldersManager->deleteFolder($userImagesPath);
        $this->foldersManager->deleteFolder($workoutsImagesPath);

    }
}


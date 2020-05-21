<?php
declare(strict_types=1);

namespace App\MessageHandler\Event;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Message\Event\UserFoldersDeletedEvent;
use App\Services\FoldersManager\FoldersManagerInterface;
use App\Services\ImagesManager\ImagesConstants;

class RemoveUserFoldersWhenUserIsDeleted implements MessageHandlerInterface
{
    private $foldersManager;
    private $uploadsDirectory;

    public function __construct(FoldersManagerInterface $foldersManager, string $uploadsDirectory)
    {
        $this->foldersManager = $foldersManager;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    public function __invoke(UserFoldersDeletedEvent $event)
    {
        $userImagesPath = $this->uploadsDirectory.'/'.ImagesConstants::USERS_IMAGES.'/'.$event->getSubdirectory();
        $workoutsImagesPath = $this->uploadsDirectory.'/'.ImagesConstants::WORKOUTS_IMAGES.'/'.$event->getSubdirectory();

        $this->foldersManager->deleteFolder($userImagesPath);
        $this->foldersManager->deleteFolder($workoutsImagesPath);

    }
}


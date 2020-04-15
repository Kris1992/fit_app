<?php

namespace App\MessageHandler\Command;

use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\Command\DeleteUserFolders;
use App\Message\Event\UserFoldersDeletedEvent;
use App\Repository\UserRepository;

class DeleteUserFoldersHandler implements  MessageSubscriberInterface
{

    private $eventBus;
    private $userRepository;

    public function __construct(MessageBusInterface $eventBus, UserRepository $userRepository)
    {
        $this->eventBus = $eventBus;
        $this->userRepository = $userRepository;
    }

    public function __invoke(DeleteUserFolders $deleteUserFolders)
    {
        $subdirectory = $deleteUserFolders->getSubdirectory();
        $user = $this->userRepository->findOneBy(['login' => $subdirectory]);

        //check is user successfully deleted before
        if($user) {
            throw new \Exception("User still exist in database");
        }

        $this->eventBus->dispatch(new UserFoldersDeletedEvent($subdirectory));
    }

    public static function getHandledMessages(): iterable
    {
        yield DeleteUserFolders::class => [
            'method' => '__invoke',
        ];
    }
}
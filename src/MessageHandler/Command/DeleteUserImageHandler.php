<?php

namespace App\MessageHandler\Command;

use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\Command\DeleteUserImage;
use App\Repository\UserRepository;
//use Psr\Log\LoggerAwareInterface;
//use Psr\Log\LoggerAwareTrait;
use App\Message\Event\UserImageFilenameDeletedEvent;

use Psr\Log\LoggerInterface;

class DeleteUserImageHandler implements  MessageSubscriberInterface//, LoggerAwareInterface 
{
    //use LoggerAwareTrait;

    private $entityManager;
    private $eventBus;
    private $userRepository;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $eventBus, UserRepository $userRepository, LoggerInterface $logger)
    {
        $this->eventBus = $eventBus;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    public function __invoke(DeleteUserImage $deleteUserImage)
    {
        $filenameGiven = $deleteUserImage->getFilename();
        $subdirectoryGiven = $deleteUserImage->getSubdirectory();
        //If filename is given user was deleted so don't need to clear filename data from db
        if($filenameGiven && $subdirectoryGiven) {
            $this->eventBus->dispatch(new UserImageFilenameDeletedEvent($filenameGiven, $subdirectoryGiven));
        } else {
            $userId = $deleteUserImage->getUserId();
            $user = $this->userRepository->findOneBy(['id' => $userId]);
            $filename = $user->getImageFilename();
            $subdirectory = $user->getLogin();

            if(!$filename || !$subdirectory) {
                if($this->logger) {
                    $this->logger->alert('Image filename or subdirectory is missing!');
                }
                throw new \Exception("Cannot delete user image: Image filename or subdirectory is missing!");
            }

            $user->setImageFilename(null);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->eventBus->dispatch(new UserImageFilenameDeletedEvent($filename, $subdirectory));
        }
    }

    public static function getHandledMessages(): iterable
    {
        yield DeleteUserImage::class => [
            'method' => '__invoke',
        ];
    }
}
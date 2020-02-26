<?php

namespace App\MessageHandler\Command;

use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\Command\DeleteUserImage;
use App\Repository\UserRepository;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use App\Message\Event\UserImageFilenameDeletedEvent;

class DeleteUserImageHandler implements  MessageSubscriberInterface, LoggerAwareInterface 
{
    use LoggerAwareTrait;

    private $entityManager;
    private $eventBus;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $eventBus, UserRepository $userRepository)
    {
        $this->eventBus = $eventBus;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function __invoke(DeleteUserImage $deleteUserImage)
    {

        $userId = $deleteUserImage->getUserId();

        $user = $this->userRepository->findOneBy(['id' => $userId]);

        $filename = $user->getImageFilename();

        if(!$filename) {
            if($this->logger) {
                $this->logger->alert(sprintf('Image filename %d is missing!', $filename));
            }
            return;
        }

        $user->setImageFilename(null);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new UserImageFilenameDeletedEvent($filename));
    }

    public static function getHandledMessages(): iterable
    {
        yield DeleteUserImage::class => [
            'method' => '__invoke',
        ];
    }
}
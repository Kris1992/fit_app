<?php
declare(strict_types=1);

namespace App\MessageHandler\Command;

use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\Command\CheckIsAttachmentUsed;
use App\Repository\AttachmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Message\Event\AttachmentDeletedEvent;

class CheckIsAttachmentUsedHandler implements  MessageSubscriberInterface
{

    private $eventBus;
    private $attachmentRepository;
    private $entityManager;
    private $logger;

    public function __construct(MessageBusInterface $eventBus, AttachmentRepository $attachmentRepository, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->eventBus = $eventBus;
        $this->attachmentRepository = $attachmentRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function __invoke(CheckIsAttachmentUsed $checkIsAttachmentUsed)
    {
        $attachmentId = $checkIsAttachmentUsed->getId();

        $attachment = $this->attachmentRepository->findOneBy(['id' => $attachmentId]);

        if(!$attachment) {
            throw new \Exception("Cannot find given attachment.");
        }
        if ($attachment->getCuriosity()) {
            if($this->logger) {
                $this->logger->info(sprintf('Attachment with ID: %d was used.', $attachmentId));
            }
        } else {
            $filename = $attachment->getFilename();
            $this->entityManager->remove($attachment);
            $this->entityManager->flush();

            $this->eventBus->dispatch(new AttachmentDeletedEvent($checkIsAttachmentUsed->getSubdirectory(), $filename));
        }
    }

    public static function getHandledMessages(): iterable
    {
        yield CheckIsAttachmentUsed::class => [
            'method' => '__invoke',
        ];
    }
}
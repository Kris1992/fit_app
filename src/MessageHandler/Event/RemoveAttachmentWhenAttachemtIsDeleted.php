<?php

namespace App\MessageHandler\Event;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Message\Event\AttachmentDeletedEvent;
use App\Services\AttachmentsManager\AttachmentsManagerInterface;

class RemoveAttachmentWhenAttachemtIsDeleted implements MessageHandlerInterface
{
    private $attachmentsManager;

    public function __construct(AttachmentsManagerInterface $attachmentsManager)
    {
        $this->attachmentsManager = $attachmentsManager;
    }

    public function __invoke(AttachmentDeletedEvent $event)
    {
        $subPath = $event->getSubdirectory().'/'.$event->getFilename();

        $this->attachmentsManager->delete($subPath);
    }
}


<?php
declare(strict_types=1);

namespace App\Services\AttachmentsHelper;

use App\Services\AttachmentsManager\AttachmentsManagerInterface;
use App\Repository\AttachmentRepository;
use App\Entity\Curiosity;
use Psr\Log\LoggerInterface;

class AttachmentsHelper implements AttachmentsHelperInterface {

    private $attachmentRepository;
    private $attachmentsManager;
    private $logger;

    /**
     * AttachmentsHelper Constructor
     *
     *@param AttachmentRepository $attachmentRepository
     *@param AttachmentsManagerInterface $attachmentsManager
     *@param LoggerInterface $logger
     *
     */
    public function __construct(AttachmentRepository $attachmentRepository, AttachmentsManagerInterface $attachmentsManager, LoggerInterface $logger)
    {
        $this->attachmentRepository = $attachmentRepository;
        $this->attachmentsManager = $attachmentsManager;
        $this->logger = $logger;
    }

    public function getAttachments(string $content): ?Array
    {

        $pattern = '~< *img[^>]*src *= *["\']?([^"\']*)~';
        preg_match_all($pattern, $content, $matches);
        if ($matches) {
            $filenames = array_map(function($match) {
                return basename($match);
            }, $matches[1]);
            return $filenames;
        }
       
        return null;
    }

    public function addNewAttachments(Curiosity $curiosity, Array $filenames): Curiosity
    {
        $attachments = $this->attachmentRepository->findAllByFilenames($filenames);

        foreach ($attachments as $attachment) {
            $curiosity->addAttachment($attachment);
        }   

        return $curiosity;
    }

    public function removeUnusedAttachments(Curiosity $curiosity, ?Array $filenames): Curiosity
    {
        if ($filenames) {
            $attachments = $this->attachmentRepository->findAllNotInFilenamesByCuriosity($filenames, $curiosity);
        } else {
            $attachments = $this->attachmentRepository->findBy(['curiosity' => $curiosity]);
        }

        if ($attachments) {
            $author = $curiosity->getAuthor();
            foreach ($attachments as $attachment) {
                $isDeleted = $this->attachmentsManager->delete($author->getLogin().'/'.$attachment->getFilename());

                if (!$isDeleted) {
                    $this->logger->alert(sprintf('Cannot delete attachment %s !', $attachment->getFilename()));
                }

                $curiosity->removeAttachment($attachment);
            }   
        }

        return $curiosity;
    }  
}
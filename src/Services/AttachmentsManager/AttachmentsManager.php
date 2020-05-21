<?php
declare(strict_types=1);

namespace App\Services\AttachmentsManager;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\FilesManager\FilesManagerInterface;

class AttachmentsManager implements AttachmentsManagerInterface {

    const ATTACHMENTS_DIR = 'attachments';

    private $filesManager;
    private $uploadsDirectory;

    /**
     * AttachmentsManager Constructor
     *
     *@param FilesManagerInterface $filesManager
     *@param string $uploadsDirectory Path to uploads directory
     *
     */
    public function __construct(FilesManagerInterface $filesManager, string $uploadsDirectory)  
    {
        $this->filesManager = $filesManager;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    public function upload(File $file, string $subdirectory): ?Array
    {   
        $attachmentDirectory = $this->uploadsDirectory.'/'.self::ATTACHMENTS_DIR.'/'.$subdirectory.'/';
        $filename = 'attachment-'.uniqid().'.'.$file->guessExtension();
        $isMoved = $this->filesManager->moveTo($file, $attachmentDirectory, $filename);

        if (!$isMoved) {
            return null;
        }

        $pathAndFilename = [
            'partialPath' => '/uploads/'.self::ATTACHMENTS_DIR.'/'.$subdirectory.'/'.$filename,
            'filename' => $filename
        ];

        return $pathAndFilename;
    }

    public function delete(string $subPath): bool
    {
        try {
            $this->filesManager->delete($subPath, self::ATTACHMENTS_DIR);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
<?php

namespace App\Services\FilesManager;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\FoldersManager\FoldersManagerInterface;
use Psr\Log\LoggerInterface;
use Gedmo\Sluggable\Util\Urlizer;

class FilesManager implements FilesManagerInterface {

    private $publicFilesystem;
    private $logger;
    private $foldersManager;
    private $uploadsDirectory;

    /**
     * FilesManager Constructor
     *
     *@param FilesystemInterface $publicUploadsFilesystem
     *@param LoggerInterface $logger
     *@param FoldersManagerInterface $foldersManager
     *@param string $uploadsDirectory Path to uploads directory
     *
     */
    public function __construct(FilesystemInterface $publicUploadsFilesystem, LoggerInterface $logger, FoldersManagerInterface $foldersManager, string $uploadsDirectory)  
    {
        $this->publicFilesystem = $publicUploadsFilesystem;
        $this->logger = $logger;
        $this->foldersManager = $foldersManager;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    public function upload(File $file, string $folderName): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilenameExtFree = $this->clearFilename($originalFilename);

        $extension = $file->guessExtension();
        $newFilename = $newFilenameExtFree.'.'.$extension;
        
        $this->createDir($folderName);

        $stream = fopen($file->getPathname(), 'r');
        $result = $this->publicFilesystem->writeStream(
            $folderName.'/'.$newFilename,
            $stream
        );

        if ($result === false) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
        }
        if (is_resource($stream)) {
            fclose($stream);
        }

        return $newFilename;
    }

    public function delete(string $existingFilename, string $foldersPath): void
    {
        $filePath = $foldersPath.'/'.$existingFilename;

        try {
            $result = $this->publicFilesystem->delete($filePath);
            if ($result === false) {
                $message = sprintf('Could not delete old uploaded file "%s"', $existingFilename);
                $this->logger->alert($message);
                throw new \Exception($message);
            }
        } catch (FileNotFoundException $e) {
            $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
        }   
    }

    public function getAbsolutePath(string $path): string
    {   
        return $this->uploadsDirectory.'/'.$path;
    }

    public function moveTo(File $file, string $destinationPath, ?string $filename): bool
    {
        if (!$filename) {
            if ($file instanceof UploadedFile) {
                $filename = $file->getClientOriginalName();
            } else {
                $filename = $file->getFilename();
            }
        } 

        try {
            $file->move(
                $destinationPath,
                $filename
            );
        } catch (FileException $e) {
            return false;
        }
        
        return true;
    }

    /**
     * createDir create folder from folderName 
     * @param  string $folderName Name of folder to create
     * @return void
     */
    private function createDir(string $folderName): void
    {
        $destinationFolder = $this->uploadsDirectory.'/'.$folderName;
        $this->foldersManager->createFolder($destinationFolder);
    }

    /**
     * clearFilename Clear filename form dots and generate unique name 
     * @param  string $filename Name of uploaded file
     * @return string Cleared filename
     */
    public static function clearFilename(string $filename): string
    {
        $clearFilename = str_replace('.', '_', $filename);
        $clearFilename = Urlizer::urlize(pathinfo($clearFilename, PATHINFO_FILENAME)).'-'.uniqid();
        
        return $clearFilename;
    } 
}


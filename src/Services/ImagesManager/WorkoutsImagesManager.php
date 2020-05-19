<?php

namespace App\Services\ImagesManager;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Psr\Log\LoggerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Asset\Context\RequestStackContext;
use App\Services\ImagesResizer\ImagesResizerInterface;
use App\Services\FilesManager\FilesManager;

class WorkoutsImagesManager extends ImagesConstants implements ImagesManagerInterface
{

    private $publicFilesystem;
    private $logger;
    private $publicAssetBaseUrl;
    private $requestStackContext;
    private $imagesResizer;
    private $uploadsDirectory;

    /**
     * WorkoutsImagesManager Constructor
     *
     *@param FilesystemInterface $publicUploadsFilesystem
     *@param LoggerInterface $logger
     *@param RequestStackContext $requestStackContext
     *@param magesResizerInterface $imagesResizer
     *@param string $uploadedAssetsBaseUrl
     *@param string $uploadsDirectory
     *
     */
    public function __construct(FilesystemInterface $publicUploadsFilesystem, LoggerInterface $logger,  RequestStackContext $requestStackContext, ImagesResizerInterface $imagesResizer, string $uploadedAssetsBaseUrl, string $uploadsDirectory)  
    {
        $this->publicFilesystem = $publicUploadsFilesystem;
        $this->logger = $logger;
        $this->requestStackContext = $requestStackContext;
        $this->publicAssetBaseUrl = $uploadedAssetsBaseUrl;
        $this->uploadsDirectory = $uploadsDirectory;
        $this->imagesResizer = $imagesResizer;
    }

    /**
     * uploadImage Upload workout image and compress it to smaller one thumb image if it is too large
     * @param  File    $file             Uploaded file
     * @param  string  $existingFilename Filename of image which was uploaded before[optional]
     * @param  string  $subdirectory     Subdirectory for image[optional]
     * @param  integer $newWidth         Width of compressed image [optional]
     * @return string                    New filename
     */
    public function uploadImage(File $file, ?string $existingFilename, ?string $subdirectory, $newWidth = 150): string
    {
        if($subdirectory) {
            $directory =  self::WORKOUTS_IMAGES.'/'.$subdirectory;
            $newFilename = $this->uploadFile($file, $directory, $newWidth);
        } else {
            $this->logger->alert('Workouts image uploader: Subdirectory missing, cannot upload image!!');
            return null;
        }
        
        if ($existingFilename) {
           $result = $this->deleteOldImage($existingFilename, $subdirectory);
           if(!$result) {
                $this->logger->alert(sprintf('User upload new file but deleting old one fails. Check file: "%s" exist!!', $existingFilename));
           }
        }

        return $newFilename;
    }

    /**
     * deleteUserImage Delete user images (original and compressed) from server 
     * @param  string $existingFilename Filename of image to delete
     * @param  string  $subdirectory     Subdirectory for image[optional]
     * @return bool                   
     */
    public function deleteImage(string $existingFilename, ?string $subdirectory): bool
    {
        if ($existingFilename && $subdirectory) {
           return $this->deleteOldImage($existingFilename, $subdirectory);
        }
        return false;
    }

    /**
     * resizeImageFromPath Resize and compress image from absolute path to original one
     * @param  string $absolutePath Absolute path to image to resize
     * @param  int    $newWidth     New width
     * @return string If image was completely resized return filename
     * @throws Exception If the given path is not a file or cannot resize it
     */
    public function resizeImageFromPath(string $absolutePath, int $newWidth): string
    {   
        $file = new File($absolutePath);
        $filename = $file->getFilename();
        $extension = $file->guessExtension();
        //$filename = $filenameExtFree.'.'.$extension;

        try {
            $this->imagesResizer->compressImage($absolutePath, $extension, $newWidth, null);
        } catch (\Exception $e) {
            throw new \Exception("Cannot resize this image.");   
        }
        
        return $filename;
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
            ->getBasePath().$this->publicAssetBaseUrl.'/'.$path;
    }

    /**
     * uploadFile Function which take care about upload image process
     * @param  File   $file      Uploaded file
     * @param  string $directory Destination directory
     * @param  int    $newWidth  Width of compress image
     * @return string            
     */
    private function uploadFile(File $file, string $directory, int $newWidth): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilenameExtFree = FilesManager::clearFilename($originalFilename);

        $extension = $file->guessExtension();
        $newFilename = $newFilenameExtFree.'.'.$extension;

        $stream = fopen($file->getPathname(), 'r');
        $result = $this->publicFilesystem->writeStream(
            $directory.'/'.$newFilename,
            $stream
        );

        if ($result === false) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
        }
        if (is_resource($stream)) {
            fclose($stream);
        }

        $path = $this->uploadsDirectory.'/'.$directory.'/'.$newFilename;
        $this->imagesResizer->compressImage($path, $extension, $newWidth, null);

        return $newFilename;
    }

    /**
     * deleteOldImage  Delete images (original and compressed) from server
     * @param  string $existingFilename Filename of image
     * @param  string  $subdirectory     Subdirectory for image
     * @return bool
     */
    private function deleteOldImage(string $existingFilename, string $subdirectory): bool
    {
        if (!$subdirectory) {
            return false;
        }

        try {
            $result = $this->publicFilesystem->delete(self::WORKOUTS_IMAGES.'/'.$subdirectory.'/'.$existingFilename);
            $resultThumb = $this->publicFilesystem->delete(self::WORKOUTS_IMAGES.'/'.$subdirectory.'/'.self::THUMB_IMAGES.'/'.$existingFilename);

            if ($result === false || $resultThumb === false) {
                throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFilename));
            }
        } catch (FileNotFoundException | \Exception $e) {
            $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
            
            return false;
        }

        return true;   
    }

}
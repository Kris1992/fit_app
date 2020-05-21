<?php
declare(strict_types=1);

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

class CuriositiesImagesManager extends ImagesConstants implements ImagesManagerInterface
{

    private $publicFilesystem;
    private $logger;
    private $publicAssetBaseUrl;
    private $requestStackContext;
    private $imagesResizer;
    private $uploadsDirectory;

    /**
     * CuriositiesImagesManager Constructor
     *
     *@param FilesystemInterface $publicUploadsFilesystem
     *@param LoggerInterface $logger
     *@param RequestStackContext $requestStackContext
     *@param ImagesResizerInterface $imagesResizer
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
     * uploadImage Upload curiosity image and compress it to smaller one thumb image if it is too large
     * @param  File    $file             Uploaded file
     * @param  string  $existingFilename Filename of image which was uploaded before[optional]
     * @param  string  $subdirectory     Subdirectory for image[optional]
     * @param  integer $newWidth         Width of compressed image [optional]
     * @return string                    New filename
     */
    public function uploadImage(File $file, ?string $existingFilename, ?string $subdirectory, $newWidth = 350): string
    {
        if($subdirectory) {
            $directory =  self::CURIOSITIES_IMAGES.'/'.$subdirectory;
            $newFilename = $this->uploadFile($file, $directory, $newWidth);
        } else {
            $this->logger->alert('Curiosities image uploader: Subdirectory missing, cannot upload image!!');
            return null;
        }

        if ($existingFilename) {
           $result = $this->deleteOldImage($existingFilename, $subdirectory);
           if(!$result) {
                $this->logger->alert(sprintf('User upload new curiosity image but deleting old one fails. Check file: "%s" exist!!', $existingFilename));
           }
        }

        return $newFilename;
    }

    /**
     * deleteImage Delete curiosity images (original and compressed) from server 
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
     * @throws Exception If the given path is not a file
     */
    public function resizeImageFromPath(string $absolutePath, int $newWidth): string
    {
        //To implement
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

        $newHeight = intval($newWidth / 1.55);// Proper ratio to show image on main page
        $path = $this->uploadsDirectory.'/'.$directory.'/'.$newFilename;
        $this->imagesResizer->compressImage($path, $extension, $newWidth, $newHeight);

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
            $result = $this->publicFilesystem->delete(self::CURIOSITIES_IMAGES.'/'.$subdirectory.'/'.$existingFilename);
            $resultThumb = $this->publicFilesystem->delete(self::CURIOSITIES_IMAGES.'/'.$subdirectory.'/'.self::THUMB_IMAGES.'/'.$existingFilename);
            if ($result === false || $resultThumb === false) {
                throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFilename));
            }
        } catch (FileNotFoundException  | \Exception $e) {
            $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
            
            return false;
        }

        return true;   
    }
    
}
<?php
namespace App\Services;


use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Psr\Log\LoggerInterface;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Asset\Context\RequestStackContext;

class UploadImagesHelper
{
    const USERS_IMAGES = 'users_images';

    private $publicFilesystem;
    private $logger;
    private $publicAssetBaseUrl;
    private $requestStackContext;

    /**
     * UploadImagesHelper Constructor
     *
     *@param FilesystemInterface $publicUploadsFilesystem
     *@param LoggerInterface $logger
     *@param string $uploadedAssetsBaseUrl
     *@param RequestStackContext $requestStackContext
     *
     */
    public function __construct(FilesystemInterface $publicUploadsFilesystem, LoggerInterface $logger, string $uploadedAssetsBaseUrl, RequestStackContext $requestStackContext)  
    {
        $this->publicFilesystem = $publicUploadsFilesystem;
        $this->logger = $logger;
        $this->requestStackContext = $requestStackContext;
        $this->publicAssetBaseUrl = $uploadedAssetsBaseUrl;
    }

    public function uploadUserImage(File $file, ?string $existingFilename): string
    {

        $newFilename = $this->uploadFile($file, self::USERS_IMAGES);

        if ($existingFilename) {
             try {
                $result = $this->publicFilesystem->delete(self::USERS_IMAGES.'/'.$existingFilename);
                if ($result === false) {
                    throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFilename));
                }

            } catch (FileNotFoundException $e) {
                $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
            }
        }

        return $newFilename;
    }

    private function uploadFile(File $file, string $directory): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }
        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)).'-'.uniqid().'.'.$file->guessExtension();
        
        $filesystem = $this->publicFilesystem;

        $stream = fopen($file->getPathname(), 'r');
        $result = $filesystem->writeStream(
            $directory.'/'.$newFilename,
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

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
            ->getBasePath().$this->publicAssetBaseUrl.'/'.$path;
    }

}


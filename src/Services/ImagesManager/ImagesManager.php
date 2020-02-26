<?php

namespace App\Services\ImagesManager;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Psr\Log\LoggerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Asset\Context\RequestStackContext;


class ImagesManager implements ImagesManagerInterface
{
    const USERS_IMAGES = 'users_images';
    const THUMB_IMAGES = 'thumb';
    const TEMP_GIF = 'gif_temp';
    const JPEG_QUALITY = 75;
    const PNG_QUALITY = 0;

    private $publicFilesystem;
    private $logger;
    private $publicAssetBaseUrl;
    private $requestStackContext;
    private $uploadsDirectory;

    /**
     * ImagesManager Constructor
     *
     *@param FilesystemInterface $publicUploadsFilesystem
     *@param LoggerInterface $logger
     *@param string $uploadedAssetsBaseUrl
     *@param RequestStackContext $requestStackContext
     *
     */
    public function __construct(FilesystemInterface $publicUploadsFilesystem, LoggerInterface $logger, string $uploadedAssetsBaseUrl, RequestStackContext $requestStackContext, string $uploadsDirectory)  
    {
        $this->publicFilesystem = $publicUploadsFilesystem;
        $this->logger = $logger;
        $this->requestStackContext = $requestStackContext;
        $this->publicAssetBaseUrl = $uploadedAssetsBaseUrl;
        $this->uploadsDirectory = $uploadsDirectory;
    }

    /**
     * uploadUserImage Upload user image and compress it to smaller one thumb image if it is too large
     * @param  File    $file             Uploaded file
     * @param  string  $existingFilename Filename of image which was uploaded before[optional]
     * @param  integer $newWidth         Width of compressed image [optional]
     * @return string                    New filename
     */
    public function uploadUserImage(File $file, ?string $existingFilename, $newWidth = 100): string
    {
        $newFilename = $this->uploadFile($file, self::USERS_IMAGES, $newWidth);

        if ($existingFilename) {
           $result = $this->deleteOldImage($existingFilename);
           if(!$result) {
                $this->logger->alert(sprintf('User upload new file but deleting old one fails. Check file: "%s" exist!!', $existingFilename));
           }
        }

        return $newFilename;
    }

    /**
     * deleteUserImage Delete user images (original and compressed) from server 
     * @param  string $existingFilename Filename of image to delete
     * @return bool                   
     */
    public function deleteUserImage(string $existingFilename): bool
    {
        if ($existingFilename) {
           return $this->deleteOldImage($existingFilename);
        }
        return false;
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

        $newFilenameExtFree = $this->clearFilename($originalFilename);

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

        $this->compressImage($newFilenameExtFree, $extension, $newWidth);

        return $newFilename;
    }

    /**
     * deleteOldImage  Delete images (original and compressed) from server
     * @param  string $existingFilename Filename of image
     * @return bool
     */
    private function deleteOldImage(string $existingFilename): bool
    {
       try {
            $result = $this->publicFilesystem->delete(self::USERS_IMAGES.'/'.$existingFilename);
            $resultThumb = $this->publicFilesystem->delete(self::USERS_IMAGES.'/'.self::THUMB_IMAGES.'/'.$existingFilename);
            if ($result === false || $resultThumb === false) {
                throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFilename));
                
                return false;
            }
        } catch (FileNotFoundException $e) {
            $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
            
            return false;
        }

        return true;   
    }

    /**
     * compressImage Compress Image to smaller one if it is too large
     * @param  string $filename  Filename without extension
     * @param  string $extension Extension of file
     * @param  int $newWidth  New width of image
     * @return void           
     */
    private function compressImage(string $filename, string $extension, int $newWidth): void
    {
    
        if(!$this->islibraryLoaded()) {
            $this->logger->alert('Could not load GD library');
            throw new \Exception('Server library not found');
        }

        $filePath = $this->uploadsDirectory.'/'.self::USERS_IMAGES.'/';
        $source = $filePath.$filename.'.'.$extension;
        $destinationFolder = $filePath.self::THUMB_IMAGES;

        $this->createFolder($destinationFolder);

        $destination = $destinationFolder.'/'.$filename.'.'.$extension;

        try{
            //used only GD library
            if ($extension === 'jpeg') {
                $image = imagecreatefromjpeg($source);
                $newImage = $this->resizeImage($source, $image, $newWidth);
                imagejpeg($newImage, $destination, self::JPEG_QUALITY);
                $this->flushMemory($image, $newImage);
            } elseif ($extension === 'gif') {
                $gifDecoder = new GIFDecoder(fread(fopen($source, "rb"), filesize($source)));
                $delays = $gifDecoder->GIFGetDelays();
                $tempPath = $filePath.self::TEMP_GIF.'/';
            
                $iterator = 1;

                foreach ($gifDecoder->GIFGetFrames() as $frame) {
                    $tempImagePath = $tempPath.'image'.$iterator.'.gif';
                    fwrite(fopen($tempImagePath, 'wb'), $frame);
                    $this->resizeFrame($tempImagePath, $newWidth);
                    $iterator++;
                }

                $iterator = 1;

                if ($tempDir = opendir($tempPath)){
                    while (false !== ($data = readdir($tempDir))){
                        if ($data != "." && $data != ".." ) {
                            $framesTemp[] = $tempPath.'image'.$iterator.'.gif';
                            $iterator++;
                        }
                    }
                    closedir($tempDir);
                }

                $gifEncoder = new GIFEncoder($framesTemp, $delays, 0, 2, 0, 0, 0, "url");
                $fpThumb = fopen($destination, 'w');
                fwrite($fpThumb, $gifEncoder->GetAnimation());
                fclose($fpThumb);

                $this->clearFolder($tempPath);
            } elseif($extension === 'png') {
                $image = imagecreatefrompng($source);
                $newImage = $this->resizeImage($source, $image, $newWidth);
                imagepng($newImage, $destination, self::PNG_QUALITY);
                $this->flushMemory($image, $newImage);
            }
        } catch(Exception $e_img) {
            throw new Exception("Error: ".$e_img);
        }    
   }

   /**
    * resizeFrame Resize frame of gif image
    * @param  string $tempImagePath Path to image in temp folder
    * @param  int $newWidth     Target width
    * @return void
    */
   private function resizeFrame(string $tempImagePath, int $newWidth): void
   {

        list($originalWidth, $originalHeight, $imageInfo) = getimagesize($tempImagePath);

        switch ($imageInfo) {

            case 1: $img = imagecreatefromgif($tempImagePath); break;

            case 2: $img = imagecreatefromjpeg($tempImagePath);  break;

            case 3: $img = imagecreatefrompng($tempImagePath); break;

            default: throw new \Exception('Unsupported file extension');  break;
        }

        list($newWidth, $newHeight) = $this->getNewSize($originalWidth, $originalHeight, $newWidth);

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        if(($imageInfo == 1) || ($imageInfo == 3)) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage,true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($newImage, $img, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        switch ($imageInfo) {

            case 1: imagegif($newImage,$tempImagePath); break;

            case 2: imagejpeg($newImage,$tempImagePath); break;

            case 3: imagepng($newImage,$tempImagePath); break;

            default: throw new \Exception('Failed resizing image'); break;
        }

        $this->flushMemory($img, $newImage);
   }

   /**
    * flushMemory Frees any memory associated with images 
    * @param  resource $img      First resource
    * @param  resource $newImage Second resource
    * @return void
    */
   private function flushMemory($img, $newImage): void
   {
        imagedestroy($newImage);
        imagedestroy($img);
   }

   /**
    * clearFolder Remove all files in folder
    * @param  string $dirPath Path to folder
    * @return void
    */
   private function clearFolder(string $dirPath): void
   {
        foreach(glob($dirpath.'*.*') as $file){
            unlink($file);
        }
   }
   
   /**
    * [resizeImage description]
    * @param  string $source   Source filename
    * @param  resource $image    Image resource
    * @param  int $newWidth Width of new image
    * @return resource
    */
   private function resizeImage(string $source, $image, int $newWidth)
   {
    
        list($originalWidth, $originalHeight) = getimagesize($source);

        list($newWidth, $newHeight) = $this->getNewSize($originalWidth, $originalHeight, $newWidth);   

        //resize_image() 
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($newImage, false);
        $result = imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        imagesavealpha($newImage, true);
        if ($result === false) {
            throw new \Exception(sprintf('Could not compress uploaded Image "%s"', $filename));
        }
    
        return $newImage;
   }

   /**
    * getNewSize Establish new size of image (width and height) and return it
    * @param  int    $originalWidth  Original width
    * @param  int    $originalHeight Original height
    * @param  int    $newWidth       Destination width
    * @return array                 Array with new size
    */
   private function getNewSize(int $originalWidth, int $originalHeight, int $newWidth): array
   {     
        if($originalWidth > $newWidth) {
            $newHeight = ($originalHeight * $newWidth)/$originalWidth;
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }

        return array($newWidth, $newHeight);
   }

   /**
    * clearFilename Clear filename from dots and add unique id
    * @param  string $filename Original filename of uploaded file
    * @return string           New filename
    */
    private function clearFilename(string $filename): string
    {
        $clearFilename = str_replace('.', '_', $filename);
        $clearFilename = Urlizer::urlize(pathinfo($clearFilename, PATHINFO_FILENAME)).'-'.uniqid();
        
        return $clearFilename;
    } 

    /**
     * islibraryLoaded  Check library gd or gd2 can be used
     * @return bool
     */
    private function islibraryLoaded(): bool
    {
        if (!extension_loaded('gd') && !extension_loaded('gd2')) {
            return false;
        }

        return true;
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
            ->getBasePath().$this->publicAssetBaseUrl.'/'.$path;

    }

    /**
     * createFolder Check required folder exsists (if not create it)
     * @param  string $folderPath Path to required folder
     * @return void             
     */
    private function createFolder(string $folderPath): void
    {
        if(!is_dir($folderPath)) {
         mkdir($folderPath);
        }
    }

}
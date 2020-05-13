<?php

namespace App\Services\ImagesResizer;

use App\Services\FoldersManager\FoldersManagerInterface;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Psr\Log\LoggerInterface;
use Gedmo\Sluggable\Util\Urlizer;

class ImagesResizer implements ImagesResizerInterface
{
    const THUMB_IMAGES = 'thumb';
    const TEMP_GIF = 'gif_temp';
    const JPEG_QUALITY = 75;
    const PNG_QUALITY = 0;

    private $logger;
    private $foldersManager;
    private $uploadsDirectory;

    /**
     * ImagesResizer Constructor
     *
     *@param LoggerInterface $logger
     *@param FoldersManagerInterface $foldersManager
     *@param string $uploadsDirectory
     *
     */
    public function __construct(LoggerInterface $logger, FoldersManagerInterface $foldersManager, string $uploadsDirectory)  
    {
        $this->logger = $logger;
        $this->uploadsDirectory = $uploadsDirectory;
        $this->foldersManager = $foldersManager;
    }


    /**
     * compressImage Compress Image to smaller one if it is too large
     * @param  string $source  Absolute path to source file
     * @param  string $extension Extension of file
     * @param  int $newWidth  New width of image
     * @param  int $newHeight Height of new image (if given ratio of image will be changed) [optional]
     * @return void           
     * @throws Exception
     */
    public function compressImage(string $source, string $extension, int $newWidth, ?int $newHeight): void
    {
        
        if(!$this->islibraryLoaded()) {
            $this->logger->alert('Could not load GD library');
            throw new \Exception('Server library not found');
        }

        $pathInfo = pathinfo($source);
        $filePath = $pathInfo['dirname'];
        $basenameArray = explode('.', $pathInfo['basename']);
        $filenameExtFree = $basenameArray[0];
        
        $destinationFolder = $filePath.'/'.self::THUMB_IMAGES;
        
        $this->foldersManager->createFolder($destinationFolder);

        $destination = $destinationFolder.'/'.$filenameExtFree.'.'.$extension;

        try{
            //used only GD library
            if ($extension === 'jpeg') {
                $image = imagecreatefromjpeg($source);
                $newImage = $this->resizeImage($source, $image, $newWidth, $newHeight);
                imagejpeg($newImage, $destination, self::JPEG_QUALITY);
                $this->flushMemory($image, $newImage);
            } elseif ($extension === 'gif') {
                $gifDecoder = new GIFDecoder(fread(fopen($source, "rb"), filesize($source)));
                $delays = $gifDecoder->GIFGetDelays();
                $tempPath = $filePath.'/'.self::TEMP_GIF.'/';
                $this->foldersManager->createFolder($tempPath);
            
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

                dump($tempPath);
                $this->foldersManager->clearFolder($tempPath);
            } elseif($extension === 'png') {
                $image = imagecreatefrompng($source);
                $newImage = $this->resizeImage($source, $image, $newWidth, $newHeight);
                imagepng($newImage, $destination, self::PNG_QUALITY);
                $this->flushMemory($image, $newImage);
            }
        } catch(\Exception $e_img) {
            throw new \Exception("Error: ".$e_img);
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
    * [resizeImage description]
    * @param  string $source   Source filename
    * @param  resource $image    Image resource
    * @param  int $newWidth Width of new image
    * @param  int $newHeight Height of new image (if given ratio of image will be changed) [optional]
    * @return resource
    */
   private function resizeImage(string $source, $image, int $newWidth, ?int $newHeight)
   {
    
        list($originalWidth, $originalHeight) = getimagesize($source);
        
        if (!$newHeight) {
            list($newWidth, $newHeight) = $this->getNewSize($originalWidth, $originalHeight, $newWidth);
        }

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

}
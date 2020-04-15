<?php

namespace App\Services\ImagesManager;

use Symfony\Component\HttpFoundation\File\File;

/**
 *  Manage Images (upload, delete, change)
 */
interface ImagesManagerInterface
{   

    /**
     * uploadImage Upload image and compress it to smaller one thumb image if it is too large
     * @param  File    $file             Uploaded file
     * @param  string  $existingFilename Filename of image which was uploaded before[optional]
     * @param  string  $subdirectory     Subdirectory for image[optional]
     * @param  integer $newWidth         Width of compressed image [optional]
     * @return string                    New filename
     */
    public function uploadImage(File $file, ?string $existingFilename, ?string $subdirectory, int $newWidth): string;

    /**
     * deleteImage Delete images (original and compressed) from server 
     * @param  string $existingFilename Filename of image to delete
     * @param  string  $subdirectory     Subdirectory for image[optional]
     * @return bool                   
     */
    public function deleteImage(string $existingFilename, ?string $subdirectory): bool;

    /**
     * resizeImageFromPath Resize and compress image from absolute path to original one
     * @param  string $absolutePath Absolute path to image to resize
     * @param  int    $newWidth     New width
     * @return string If image was completely resized return filename
     * @throws FileNotFoundException If the given path is not a file
     */
    public function resizeImageFromPath(string $absolutePath, int $newWidth): string; 
}

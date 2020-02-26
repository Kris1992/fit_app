<?php

namespace App\Services\ImagesManager;

use Symfony\Component\HttpFoundation\File\File;

/**
 *  Manage Images (upload, delete, treatment)
 */
interface ImagesManagerInterface
{   

    /**
     * uploadUserImage Upload user image and compress it to smaller one thumb image if it is too large
     * @param  File    $file             Uploaded file
     * @param  string  $existingFilename Filename of image which was uploaded before[optional]
     * @param  integer $newWidth         Width of compressed image [optional]
     * @return string                    New filename
     */
    public function uploadUserImage(File $file, ?string $existingFilename, int $newWidth): string;

    /**
     * deleteUserImage Delete user images (original and compressed) from server 
     * @param  string $existingFilename Filename of image to delete
     * @return bool                   
     */
    public function deleteUserImage(string $existingFilename): bool;
}
<?php

namespace App\Services\AttachmentsManager;

use Symfony\Component\HttpFoundation\File\File;

/**
 *  Manage Attachments
 */
interface AttachmentsManagerInterface
{   

    /**
     * upload Upload file
     * @param  File    $file             Uploaded file
     * @param  string  $subdirectory     String with subdirectory from uploads dir
     * @return Array                     Array with filename and path
     */
    public function upload(File $file, string $subdirectory): ?Array;

    /**
     * delete Delete attachment
     * @param  string $subPath Path from attachments directory
     * @return bool
     */
    public function delete(string $subPath): bool;

}

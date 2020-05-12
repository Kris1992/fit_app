<?php

namespace App\Services\FilesManager;

use Symfony\Component\HttpFoundation\File\File;

/**
 *  Manage Files which are not images(upload, delete, treatment)
 */
interface FilesManagerInterface
{   

    /**
     * upload Upload file on the server
     * @param File   $file Uploaded file
     * @param string $folderName Name of folder (where save file?)
     * @return string New filename
     */
    public function upload(File $file, string $folderName): string;

    /**
     * delete Delete file from server
     * @param string $existingFilePath Path to file to delete
     * @param string $foldersPath paths of all folders from public path
     * @return void
     */
    public function delete(string $existingFilePath, string $foldersPath): void;

    /**
     * getAbsolutePath Get absolute path to to file from path 
     * @param  string $path Path from uploads directory
     * @return string
     */
    public function getAbsolutePath(string $path): string;

    /**
     * moveTo Move file to destionation directory 
     * @param  File   $file        File to move
     * @param  string $destinationPath Destionation directory
     * @return bool
     */
    public function moveTo(File $file, string $destinationPath, ?string $filename): bool;

}

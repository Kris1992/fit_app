<?php
declare(strict_types=1);

namespace App\Services\FoldersManager;


/**
 *  Manage folders (create, clear)
 */
interface FoldersManagerInterface
{   

    /**
     * createFolder Check required folder exists (if not create it) (just for single folder in path!) 
     * @param  string $folderPath Path to required folder
     * @return void             
     */
    public function createFolder(string $folderPath): void;

    /**
    * clearFolder Remove all files in folder
    * @param  string $folderPath Path to folder
    * @return void
    */
    public function clearFolder(string $folderPath): void;

    /**
     * createFolder Check required folders exsists (if not create it)
     * @param  string $folderPath Path to required folders
     * @return void             
     */
    public function createFolders(string $foldersPath): void;

    /**
     * deleteFolder Delete given by absolute path folder (if it exist) 
     * @param  string $folderPath Absolute path to folder
     * @return void
     */
    public function deleteFolder(string $folderPath): void;

}
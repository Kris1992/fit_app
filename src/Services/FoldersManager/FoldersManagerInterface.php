<?php

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

}
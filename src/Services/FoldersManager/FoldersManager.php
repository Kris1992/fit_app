<?php

namespace App\Services\FoldersManager;

class FoldersManager implements FoldersManagerInterface
{

   /**
    * clearFolder Remove all files in folder
    * @param  string $dirPath Path to folder
    * @return void
    */
    public function clearFolder(string $dirPath): void
    {
        foreach(glob($dirpath.'*.*') as $file){
            unlink($file);
        }
    }
   
    /**
     * createFolder Check required folder exists (if not create it)
     * @param  string $folderPath Path to required folder
     * @return void             
     */
    public function createFolder(string $folderPath): void
    {
        if(!is_dir($folderPath)) {
            mkdir($folderPath);
        }
    }

}
<?php 

namespace App\Services\FileDecoder;

use App\Services\FoldersManager\FoldersManagerInterface;

/** 
 *  Encode file from base64
 */
class Base64FileDecoder implements FileDecoderInterface
{

    private $uploadsDirectory;
    private $foldersManager;

    /**
     * Base64FileDecoder Constructor
     * @param FoldersManagerInterface $foldersManager   
     * @param string                  $uploadsDirectory 
     */
    public function __construct(FoldersManagerInterface $foldersManager, string $uploadsDirectory)
    {
        $this->uploadsDirectory = $uploadsDirectory;
        $this->foldersManager = $foldersManager;
    }

    public function decode(string $encodedString, string $targetPath): ?string
    {
        $imageData = explode( ',', $encodedString);
        $image = base64_decode($imageData[1]);
        if (!$imageData[1]) {
            return null;
        }
        
        $this->createDir($targetPath);
        $imagePath = $this->uploadsDirectory.'/'.$targetPath.uniqid().'.png';
        $openFile = fopen($imagePath, 'wb');
        fwrite($openFile, $image);
        fclose($openFile);
        
        return $imagePath;
    }

    /**
     * createDir Create all needed folders from folderStructure
     * @param  string $folderStructure Path with names with folders to create
     * @return void
     */
    private function createDir(string $folderStructure): void
    {
        $destinationFolders = $this->uploadsDirectory.'/'.$folderStructure;
        $this->foldersManager->createFolders($destinationFolders);
    }

}
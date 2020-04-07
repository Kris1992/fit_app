<?php 

namespace App\Services\FileDecoder;

/** 
 *  Encode file from base64
 */
class Base64FileDecoder implements FileDecoderInterface
{

    private $uploadsDirectory;

    public function __construct(string $uploadsDirectory)
    {
        $this->uploadsDirectory = $uploadsDirectory;
    }

    public function decode(string $encodedString, string $tagetPath): ?string
    {
        $imageData = explode( ',', $encodedString);
        $image = base64_decode($imageData[1]);
        if (!$imageData[1]) {
            return null;
        }
        
        $this->createDir($tagetPath);
        $imagePath = $this->uploadsDirectory.'/'.$tagetPath.uniqid().'.jpeg';
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
        dump($folderStructure);
        $destinationFolders = $this->uploadsDirectory.'/'.$folderStructure;
        $this->createFolders($destinationFolders);
    }

    /**
     * createFolder Check required folders exsists (if not create it)
     * @param  string $folderPath Path to required folders
     * @return void             
     */
    private function createFolders(string $foldersPath): void
    {
        if(!is_dir($foldersPath)) {
            mkdir($foldersPath, 0777, true);

        }
    }

}
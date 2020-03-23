<?php

namespace App\Services\FileReader;

use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Psr\Log\LoggerInterface;
use App\Services\FilesManager\FilesManagerInterface;

class CSVFileReader implements FileReaderInterface
{

    private $logger;
    private $file;
    private $header;

    const MAX_LINE_LENGTH = 1000;


    /**
     * CSVFileReader Constructor
     * 
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)  
    {
        $this->logger = $logger;
    }

    public function __destruct()
    {
        if ($this->file) {
            fclose($this->file);
        }
    }


    public function read(string $absoluteFilePath): void
    {
        $this->file = fopen($absoluteFilePath, "r");

        //Get properties names 
        $this->header = fgetcsv($this->file, self::MAX_LINE_LENGTH, ",");
    }

    public function parseToArray(): array
    {   
        $dataArray = array();

        while (($rowData = fgetcsv($this->file, self::MAX_LINE_LENGTH, ",")) !== FALSE) {
            foreach ($this->header as $i => $property) {
                    $rowDataAssoc[$property] = $rowData[$i];
                }
            $dataArray[] = $rowDataAssoc;
        }

        return $dataArray;
            
    }
}




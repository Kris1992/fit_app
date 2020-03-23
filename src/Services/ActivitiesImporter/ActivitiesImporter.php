<?php

namespace App\Services\ActivitiesImporter;

use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Psr\Log\LoggerInterface;
use App\Services\FilesManager\FilesManagerInterface;
use App\Services\FileReader\FileReaderInterface;

class ActivitiesImporter implements ActivitiesImporterInterface
{

    private $logger;
    private $filesManagerInterface;
    private $uploadsDirectory;
    private $csvFileReader;


    /**
     * ActivitiesImporter Constructor
     * 
     * @param LoggerInterface $logger
     * @param FilesManagerInterface $filesManagerInterface
     */
    public function __construct(LoggerInterface $logger, FilesManagerInterface $filesManagerInterface, FileReaderInterface $csvFileReader, string $uploadsDirectory)  
    {
        $this->logger = $logger;
        $this->filesManagerInterface = $filesManagerInterface;
        $this->uploadsDirectory = $uploadsDirectory;
        $this->csvFileReader = $csvFileReader;
    }

    public function import(File $file): void
    {
        /*
        upload 
        check by csvlint and curl
        read and bind to model
        validate model
        save to db
         */
        try {
            $filename = $this->filesManagerInterface->upload($file, 'activity_csv');
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

        $absoluteFilePath = $this->uploadsDirectory.'/activity_csv/'.$filename;

        //To do something new I decide to validate csv file by using CSVLint api and curl
        $isValidCSV = $this->curlCSVValidator($absoluteFilePath);
        
        if($isValidCSV) {
            $this->csvFileReader->read($absoluteFilePath);
            $array = $this->csvFileReader->parseToArray();

        }

        $this->filesManagerInterface->delete($filename, 'activity_csv');
        
        if (!$isValidCSV) {
            throw new Exception("Uploaded CSV file is not valid");
        }
    }

    private function curlCSVValidator(string $absoluteFilePath): bool
    {
        //curl -F --data "files[]=filePath" http://csvlint.io/package.json

        $curl = curl_init();
        $url = 'http://csvlint.io/package';
        curl_setopt($curl, CURLOPT_URL, $url);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");   
        curl_setopt($curl, CURLOPT_HTTPHEADER,array('Content-Type: multipart/form-data'));
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);   
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);  
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $curlFile = new \CURLFile($absoluteFilePath);
        $postArray = [
            'files[]' => $curlFile
        ];
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postArray);

        $result = curl_exec($curl);

        curl_close($curl);

        if(preg_match('!Your CSV is valid!', $result)) {
            return true;
        }

        return false;
        //throw new Exception("CSV is not valid or site CSVLint is disconnect");
        //logger
    }
}




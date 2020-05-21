<?php
declare(strict_types=1);

namespace App\Services\FileReader;

use App\Services\FilesManager\FilesManagerInterface;

class CSVFileReader implements FileReaderInterface
{

    private $file;
    private $header;

    const MAX_LINE_LENGTH = 1000;

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
                    $rowDataAssoc[$property] = ucfirst($rowData[$i]);
                }
            $dataArray[] = $rowDataAssoc;
        }

        return $dataArray;     
    }
}


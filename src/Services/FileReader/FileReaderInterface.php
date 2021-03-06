<?php
declare(strict_types=1);

namespace App\Services\FileReader;

/**
 *  Read file line by line
 */
interface FileReaderInterface
{   

    /**
     * read Read file
     * @param  string $path Absolute path to file to read
     * @return void
     */
    public function read(string $absoluteFilePath): void;

    /**
     * parseToArray Parse data from file to array
     * @return array Array with data from file
     */
    public function parseToArray(): array;

}
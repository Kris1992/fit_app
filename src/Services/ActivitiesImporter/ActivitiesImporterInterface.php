<?php

namespace App\Services\ActivitiesImporter;

use Symfony\Component\HttpFoundation\File\File;

/**
 *  Realize imports activities from file
 */
interface ActivitiesImporterInterface
{   

    /**
     * import Import activities from file to db
     * @param  File   $file Uploaded file
     * @return array Array with result data (imported correctly, invalid row, invalid message)
     */
    public function import(File $file): array;
}

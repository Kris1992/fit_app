<?php

namespace App\Services\ImagesResizer;

/**
 *  Resize and compress images
 */
interface ImagesResizerInterface
{   

    /**
     * compressImage Compress image to smaller one thumb image
     * @param  string  $source  Absolute path to source file 
     * @param  string  $extension  Extension of file
     * @param  integer $newWidth  Width of compressed image
     * @return void
     */
    public function compressImage(string $source, string $extension, int $newWidth): void;

}

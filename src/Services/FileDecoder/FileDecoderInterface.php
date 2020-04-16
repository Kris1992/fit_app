<?php 

namespace App\Services\FileDecoder;

/** 
 *  Decode file e.g from base64 string
 */
interface FileDecoderInterface
{
    /**
     * decode Decode file from string to file object
     * @param  string $encodedString Encoded string to decode
     * @param  string $targetPath Path to folder to upload from public path
     * @return string|null Path to the file
     */
    public function decode(string $encodedString, string $tagetPath): ?string;

}

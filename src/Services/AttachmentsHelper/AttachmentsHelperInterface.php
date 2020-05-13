<?php

namespace App\Services\AttachmentsHelper;

use App\Entity\Curiosity;

/**
 *  Provide helpers methods
 */
interface AttachmentsHelperInterface
{   

    /**
     * getAttachments Get attachments from content
     * @param  string $content String with curiosity content
     * @return Array|null
     */
    public function getAttachments(string $content): ?Array;

    /**
     * addNewAttachments Add new attachments to curiosity
     * @param Curiosity $curiosity Curiosity object
     * @param Array     $filenames Array with filenames of new attachments
     * @return Curiosity
     */
    public function addNewAttachments(Curiosity $curiosity, Array $filenames): Curiosity;

    /**
     * removeUnusedAttachments Remove unused attachments from curiosity
     * @param Curiosity $curiosity Curiosity object
     * @param Array|null     $filenames Array with filenames of all used attachments or null
     * @return Curiosity
     */
    public function removeUnusedAttachments(Curiosity $curiosity, ?Array $filenames): Curiosity; 

}


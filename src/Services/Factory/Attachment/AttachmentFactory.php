<?php

namespace App\Services\Factory\Attachment;

use App\Form\Model\Attachment\AttachmentFormModel;
use App\Entity\Attachment;

class AttachmentFactory implements AttachmentFactoryInterface 
{

    public function create(AttachmentFormModel $attachmentModel): Attachment
    {
   
        $attachment = new Attachment();
        $attachment
            ->setFilename($attachmentModel->getFilename());
            ;

        return $attachment;
    }
}

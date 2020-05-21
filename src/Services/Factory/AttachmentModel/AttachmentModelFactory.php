<?php
declare(strict_types=1);

namespace App\Services\Factory\AttachmentModel;

use App\Form\Model\Attachment\AttachmentFormModel;

class AttachmentModelFactory implements AttachmentModelFactoryInterface 
{
    
    public function createFromData(string $filename): AttachmentFormModel
    {
   
        $attachmentModel = new AttachmentFormModel();
        $attachmentModel
            ->setFilename($filename);
            ;

        return $attachmentModel;
    }
}

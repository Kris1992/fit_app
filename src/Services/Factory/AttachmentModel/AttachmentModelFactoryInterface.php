<?php

namespace App\Services\Factory\AttachmentModel;

use App\Form\Model\Attachment\AttachmentFormModel;

/**
 *  Manage creating of attachment models
 */
interface AttachmentModelFactoryInterface
{   

    /**
     * createFromData Create attachment model from data
     * @param string $filename String with filename of uploaded attachment
     * @return AttachmentFormModel
     */
    public function createFromData(string $filename): AttachmentFormModel;
}

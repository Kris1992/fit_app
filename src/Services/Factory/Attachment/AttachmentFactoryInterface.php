<?php
declare(strict_types=1);

namespace App\Services\Factory\Attachment;

use App\Form\Model\Attachment\AttachmentFormModel;
use App\Entity\Attachment;

/**
 *  Manage creating of attachment
 */
interface AttachmentFactoryInterface
{   

    /**
     * create Create attachment from model
     * @param AttachmentFormModel $attachmentModel Model with attachment data
     * @return Attachment
     */
    public function create(AttachmentFormModel $attachmentModel): Attachment;
}

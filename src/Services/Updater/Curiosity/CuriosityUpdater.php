<?php
declare(strict_types=1);

namespace App\Services\Updater\Curiosity;

use App\Entity\Curiosity;
use App\Form\Model\Curiosity\CuriosityFormModel;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\ImagesManager\ImagesManagerInterface;
use App\Services\AttachmentsHelper\AttachmentsHelperInterface;

class CuriosityUpdater implements CuriosityUpdaterInterface 
{

    private $imagesManager;
    private $attachmentsHelper;

    /**
     * CuriosityUpdater Constructor
     * 
     * @param ImagesManagerInterface $curiositiesImagesManager
     * @param AttachmentsHelperInterface $attachmentsHelper
     */
    public function __construct(ImagesManagerInterface $curiositiesImagesManager, AttachmentsHelperInterface $attachmentsHelper)  
    {
        $this->imagesManager = $curiositiesImagesManager;
        $this->attachmentsHelper = $attachmentsHelper;
    }

    public function update(CuriosityFormModel $curiosityModel, Curiosity $curiosity, ?File $uploadedImage): Curiosity
    {

        $curiosity
            ->setTitle($curiosityModel->getTitle())
            ->setDescription($curiosityModel->getDescription())
            ->setContent($curiosityModel->getContent())
            ->updateTimeStamp()
            ;

        $filenames = $this->attachmentsHelper->getAttachments($curiosity->getContent());
        if ($filenames) {
            $curiosity = $this->attachmentsHelper->addNewAttachments($curiosity, $filenames);
        }
        
        $curiosity = $this->attachmentsHelper->removeUnusedAttachments($curiosity, $filenames);

        if ($curiosityModel->getIsPublished()) {
            $curiosity
                ->publish()
                ;
        } else {
            $curiosity
                ->unpublish()
                ;
        }

        if($uploadedImage) {
            $subdirectory = $curiosity->getAuthor()->getLogin();
            $newFilename = $this->imagesManager->uploadImage($uploadedImage, $curiosity->getMainImageFilename(), $subdirectory);
            $curiosity->setMainImageFilename($newFilename);
        }
        
        return $curiosity;
    }
}
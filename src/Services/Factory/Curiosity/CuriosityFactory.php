<?php

namespace App\Services\Factory\Curiosity;

use App\Entity\Curiosity;
use App\Entity\User;
use App\Form\Model\Curiosity\CuriosityFormModel;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\ImagesManager\ImagesManagerInterface;
use App\Services\AttachmentsHelper\AttachmentsHelperInterface;

class CuriosityFactory implements CuriosityFactoryInterface {

    private $imagesManager;
    private $attachmentsHelper;

    /**
     * CuriosityFactory Constructor
     * 
     * @param ImagesManagerInterface $curiositiesImagesManager
     * @param AttachmentsHelperInterface $attachmentsHelper
     */
    public function __construct(ImagesManagerInterface $curiositiesImagesManager, AttachmentsHelperInterface $attachmentsHelper)  
    {
        $this->imagesManager = $curiositiesImagesManager;
        $this->attachmentsHelper = $attachmentsHelper;
    }
    
    public function create(CuriosityFormModel $curiosityModel, User $author, ?File $uploadedImage): Curiosity
    {   
        $curiosity = new Curiosity();
        $curiosity
            ->setAuthor($author)
            ->setTitle($curiosityModel->getTitle())
            ->setDescription($curiosityModel->getDescription())
            ->setContent($curiosityModel->getContent())
            ->creationTimeStamp()
            ;

        $filenames = $this->attachmentsHelper->getAttachments($curiosity->getContent());
        if ($filenames) {
            $curiosity = $this->attachmentsHelper->addNewAttachments($curiosity, $filenames);
        }

        if ($curiosityModel->getIsPublished()) {
            $curiosity
                ->publish()
                ;
        }

        if ($uploadedImage) {
            $subdirectory = $curiosity->getAuthor()->getLogin();
            $newFilename = $this->imagesManager->uploadImage($uploadedImage, null, $subdirectory);
            $curiosity->setMainImageFilename($newFilename);
        }        

        return $curiosity;
    }    
}

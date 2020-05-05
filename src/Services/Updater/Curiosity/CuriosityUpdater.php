<?php

namespace App\Services\Updater\Curiosity;

use App\Entity\Curiosity;
use App\Form\Model\Curiosity\CuriosityFormModel;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\ImagesManager\ImagesManagerInterface;

class CuriosityUpdater implements CuriosityUpdaterInterface 
{

    private $imagesManager;

    /**
     * CuriosityUpdater Constructor
     * 
     * @param ImagesManagerInterface $curiositiesImagesManager
     */
    public function __construct(ImagesManagerInterface $curiositiesImagesManager)  
    {
        $this->imagesManager = $curiositiesImagesManager;
    }

    public function update(CuriosityFormModel $curiosityModel, Curiosity $curiosity, ?File $uploadedImage): Curiosity
    {

        $curiosity
            ->setTitle($curiosityModel->getTitle())
            ->setDescription($curiosityModel->getDescription())
            ->setContent($curiosityModel->getContent())
            ->updateTimeStamp()
            ;

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
<?php

namespace App\Services\Updater\Curiosity;

use App\Entity\Curiosity;
use App\Form\Model\Curiosity\CuriosityFormModel;
use Symfony\Component\HttpFoundation\File\File;
use App\Services\ImagesManager\ImagesManagerInterface;

class CuriosityUpdater implements CuriosityUpdaterInterface 
{

    /**
     * CuriosityUpdater Constructor
     * 
     * @param ImagesManagerInterface $imagesManager
     */
    public function __construct()//ImagesManagerInterface $imagesManager)  
    {
        //$this->imagesManager = $imagesManager;
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

            /*if($uploadedImage) {
                $newFilename = $this->imagesManager->uploadImage($uploadedImage, $user->getImageFilename(), $user->getLogin());
                $user->setImageFilename($newFilename);
            }*/
        
        return $curiosity;
    }
}
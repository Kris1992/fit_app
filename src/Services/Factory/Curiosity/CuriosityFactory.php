<?php

namespace App\Services\Factory\Curiosity;

use App\Entity\Curiosity;
use App\Entity\User;
use App\Form\Model\Curiosity\CuriosityFormModel;
use Symfony\Component\HttpFoundation\File\File;

//use App\Services\ImagesManager\ImagesManagerInterface;

class CuriosityFactory implements CuriosityFactoryInterface {

    //private $imagesManager;

    /**
     * CuriosityFactory Constructor
     * 
     * @param ImagesManagerInterface $imagesManager
     */
    public function __construct()//ImagesManagerInterface $imagesManager)  
    {
        //$this->imagesManager = $imagesManager;
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

        if ($curiosityModel->getIsPublished()) {
            $curiosity
                ->publish()
                ;
        }

        /*
        if ($uploadedImage) {
            $newFilename = $this->imagesManager->uploadImage($uploadedImage, null, $user->getLogin());
            $user->setImageFilename($newFilename);
        }
        */

        return $curiosity;
    }
}

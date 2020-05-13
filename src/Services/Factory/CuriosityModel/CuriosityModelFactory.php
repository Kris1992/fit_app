<?php

namespace App\Services\Factory\CuriosityModel;

use App\Entity\Curiosity;
use App\Form\Model\Curiosity\CuriosityFormModel;

class CuriosityModelFactory implements CuriosityModelFactoryInterface 
{
    
    public function create(Curiosity $curiosity): CuriosityFormModel
    {
   
        $curiosityModel = new CuriosityFormModel();
        $curiosityModel
            ->setId($curiosity->getId())
            ->setAuthor($curiosity->getAuthor())
            ->setTitle($curiosity->getTitle())
            ->setDescription($curiosity->getDescription())
            ->setContent($curiosity->getContent())
            ->setIsPublished($curiosity->isPublished())
            ->setMainImageFilename($curiosity->getMainImageFilename())
            ;

        return $curiosityModel;
    }
}

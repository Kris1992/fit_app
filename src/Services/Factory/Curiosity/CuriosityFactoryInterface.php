<?php

namespace App\Services\Factory\Curiosity;

use App\Entity\Curiosity;
use App\Entity\User;
use App\Form\Model\Curiosity\CuriosityFormModel;
use Symfony\Component\HttpFoundation\File\File;

/**
 *  Manage creating of curiosities
 */
interface CuriosityFactoryInterface
{   

    /**
     * create Create curiosity 
     * @param CuriosityFormModel $curiosityModel Model with curiosity data get from form
     * @param User $author Owner of curiosity
     * @param File $uploadedImage Uploaded image [optional]
     * @return Curiosity
     */
    public function create(CuriosityFormModel $curiosityModel, User $author, ?File $uploadedImage): Curiosity;

}

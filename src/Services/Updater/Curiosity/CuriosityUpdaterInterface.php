<?php 
declare(strict_types=1);

namespace App\Services\Updater\Curiosity;

use App\Entity\Curiosity;
use App\Form\Model\Curiosity\CuriosityFormModel;
use Symfony\Component\HttpFoundation\File\File;

/** 
 *  Interface for updating Curiosity entities
 */
interface CuriosityUpdaterInterface
{
    /**
     * update Update entity class with data from model class
     * @param CuriosityFormModel $curiosityModel Model data class which will used to update 
     * entity
     * @param Curiosity $curiosity Curiosity object to update
     * @param File $uploadedImage File object with uploaded image [optional]
     * @return Curiosity
     */
     public function update(CuriosityFormModel $curiosityModel, Curiosity $curiosity, ?File $uploadedImage): Curiosity;
}


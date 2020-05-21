<?php
declare(strict_types=1);

namespace App\Services\Factory\CuriosityModel;

use App\Entity\Curiosity;
use App\Form\Model\Curiosity\CuriosityFormModel;

/**
 *  Manage creating of curiosity models
 */
interface CuriosityModelFactoryInterface
{   

    /**
     * create Create curiosity model from curiosity
     * @param Curiosity $curiosity Curiosity object
     * @return CuriosityFormModel
     */
    public function create(Curiosity $curiosity): CuriosityFormModel;
}

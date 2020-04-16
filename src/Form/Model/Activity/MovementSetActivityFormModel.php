<?php
namespace App\Form\Model\Activity;

use Symfony\Component\Validator\Constraints as Assert;

class MovementSetActivityFormModel extends AbstractActivityFormModel
{

    public function __construct()
    {
        //Energy is not needed to any calculations in this type of activity so we set it immediately to 1 
        $this->setEnergy(1);
    }

}

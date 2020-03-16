<?php

namespace App\Services\Factory\Activity;

use App\Entity\AbstractActivity;
use App\Form\Model\Activity\AbstractActivityFormModel;

/**
 *  Manage creating of activities
 */
interface ActivityAbstractFactory
{   

    /**
     * create Create activity 
     * @param  AbstractActivityFormModel $activityModel Model with activity data get from form
     * @return AbstractActivity Return activity which extends AbstractActivity
     */
    public function create(AbstractActivityFormModel $activityModel): AbstractActivity;

}
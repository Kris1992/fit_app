<?php

namespace App\Services\Factory\Activity;

use App\Entity\AbstractActivity;
/**
 *  Manage creating of activities
 */
interface ActivityAbstractFactory
{   

    /**
     * createActivity  Create activity 
     * @param  array  $activityArray Array with activity data get from form
     * @return AbstractActivity Return activity which extends AbstractActivity
     */
    public function createActivity(array $activityArray): AbstractActivity;

}
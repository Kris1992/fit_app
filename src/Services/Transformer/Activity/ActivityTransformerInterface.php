<?php

namespace App\Services\Transformer\Activity;

use App\Entity\AbstractActivity;
use App\Form\Model\Activity\AbstractActivityFormModel;

/**
 *  Transform Activity object to specific model object or reverse
 */
interface ActivityTransformerInterface
{   

    /**
     * transformToModel  Create model object from Activity object
     * @param  AbstractActivity  $activity Activity object which should be converted to right model
     * @return AbstractActivityFormModel Return activity model which extends AbstractActivityFormModel
     */
    public function transformToModel(AbstractActivity $activity): AbstractActivityFormModel;


    /**
     * transformArrayToModel  Create model object from array
     * @param  Array  $activityData Array with data of activity which should be converted to right model
     * @return AbstractActivityFormModel Return activity model which extends AbstractActivityFormModel
     */
    public function transformArrayToModel(array $activityData): AbstractActivityFormModel;

    /**
     * transformToActivity Transform model object to activity object
     * @param  AbstractActivityFormModel $activityModel Activity model which should ce transformed to 
     * right Activity object 
     * @param $activity [optional] Activity object if it's given data from model will be bind to that
     * one if it's not new instance will be given 
     * @return AbstractActivity
     */
    public function transformToActivity(AbstractActivityFormModel $activityModel, $activity=null): AbstractActivity;

}
<?php 

namespace App\Services\Updater\Challenge;

use App\Entity\Challenge;
use App\Form\Model\Challenge\ChallengeFormModel;

/** 
 *  Interface for updating Challenge entities
 */
interface ChallengeUpdaterInterface
{
    /**
     * update Update entity class with data from model class
     * @param ChallengeFormModel $challengeModel Model data class which will used to update 
     * entity
     * @param Challenge $challenge Challenge class which will be updated
     * @return Challenge
     */
    public function update(ChallengeFormModel $challengeModel, Challenge $challenge): Challenge;
}

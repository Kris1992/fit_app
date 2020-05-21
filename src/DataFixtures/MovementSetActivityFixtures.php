<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\MovementSetActivity;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Create fake data for activity with sets
 */
class MovementSetActivityFixtures extends BaseFixture
{

	private static $movementActivities = [
        'Running circuits',
        'Cycling circuits'
    ];
    
    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(2, 'movement_set_activity', function($i)
        {
            $activity = new MovementSetActivity();
            $activity
                ->setType('MovementSet')
                ->setName(self::$movementActivities[$i])
                ->setEnergy(1)
                ; 

            return $activity;
        });

        $manager->flush();
    }
}

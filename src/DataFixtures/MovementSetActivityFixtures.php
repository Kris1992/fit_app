<?php

// To Do
namespace App\DataFixtures;

use App\Entity\MovementActivity;
use Doctrine\Common\Persistence\ObjectManager;


class MovementSetActivityFixtures extends BaseFixture
{

	private static $movementActivities = [
        'Running',
        'Cycling'
    ];
    
    

    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(5, 'running_activity', function($i)
        {
            $activity = new MovementActivity();
            $activity->setType('Movement');
            $activity->setName(self::$movementActivities[0]);
            $activity->setIntensity(self::$movementIntensities[$i]['name']);
            $activity->setEnergy(
                $this->faker->numberBetween(
                    self::$movementIntensities[$i]['energy']['min'],
                    self::$movementIntensities[$i]['energy']['max']
                )
            ); 
            $activity->setSpeedAverageMin(self::$movementIntensities[$i]['min']);
            $activity->setSpeedAverageMax(self::$movementIntensities[$i]['max']);

            return $activity;
        });

        $this->createMany(5, 'cycling_activity', function($i)
        {
            $activity = new MovementActivity();
            $activity->setType('Movement');
            $activity->setName(self::$movementActivities[1]);
            $activity->setIntensity(self::$movementIntensities[$i]['name']);
            $activity->setEnergy(
                $this->faker->numberBetween(
                    self::$movementIntensities[$i]['energy']['min'],
                    self::$movementIntensities[$i]['energy']['max']
                )
            ); 
            $activity->setSpeedAverageMin(self::$movementIntensities[$i]['min']);
            $activity->setSpeedAverageMax(self::$movementIntensities[$i]['max']);

            return $activity;
        });

        $manager->flush();
    }
}

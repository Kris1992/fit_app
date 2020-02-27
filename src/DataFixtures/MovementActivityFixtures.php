<?php

namespace App\DataFixtures;


use App\Entity\MovementActivity;
use Doctrine\Common\Persistence\ObjectManager;


class MovementActivityFixtures extends BaseFixture
{

	private static $movementActivities = [
        'Running',
        'Cycling'
    ];
    private static $movementIntensities = [
        'Very slow',
        'Slow',
        'Normal',
        'Fast',
        'Very fast',
    ];

    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(10, 'movement_activity', function($i)
        {
            $activity = new MovementActivity();
            $activity->setType('Movement');
            $activity->setName($this->faker->randomElement(self::$movementActivities));
            $intensity = $this->faker->randomElement(self::$movementIntensities);
            $activity->setIntensity($intensity);
            $activity->setEnergy($this->faker->numberBetween(10,400)); 
            $activity->setSpeedAverage($this->faker->numberBetween(1,20)); 

            return $activity;
        });

        $manager->flush();
    }
}

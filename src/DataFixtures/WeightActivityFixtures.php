<?php

namespace App\DataFixtures;


use App\Entity\WeightActivity;
use Doctrine\Common\Persistence\ObjectManager;


class WeightActivityFixtures extends BaseFixture
{

	private static $weightActivities = [
        'Barbell Bench Press',
        'Standing Barbell Curl'
    ];

    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(10, 'weight_activity', function($i)
        {
            $activity = new WeightActivity();
            $activity
                ->setType('Weight')
                ->setName($this->faker->randomElement(self::$weightActivities))
                ->setEnergy($this->faker->numberBetween(10,400))
                ->setRepetitions($this->faker->numberBetween(1,20)) 
                ->setWeight($this->faker->numberBetween(1,200))
                ; 

            return $activity;
        });

        $manager->flush();
    }
}

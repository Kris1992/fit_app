<?php

namespace App\DataFixtures;


use App\Entity\Activity;
use Doctrine\Common\Persistence\ObjectManager;


class ActivityFixtures extends BaseFixture
{

	private static $activities = [
        'Running',
        'Cycling',
        'Boxing',
        'Swimming',
        'Rock climbing'
    ];

    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(10, 'main_activity', function($i)
        {
            $activity = new Activity();
            $activity->setName($this->faker->randomElement(self::$activities)); //self::$activities[$i]
            $activity->setEnergy($this->faker->numberBetween(10,400)); 

            return $activity;
        });


        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;


use App\Entity\Workout;
use Doctrine\Common\Persistence\ObjectManager;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class WorkoutFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ActivityFixtures::class
        ];
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'main_workout', function($i) 
        {
            $workout = new Workout();
            $workout
                ->setUser($this->getRandomReference('main_users'))
                ->setActivity($this->getRandomReference('main_activity'))
                ->setDurationSeconds($this->faker->numberBetween($min = 1, $max = 86399))
                /* max time -> 23:59:59 */ 
                ->calculateSaveBurnoutEnergy()
                ->setStartAt($this->faker->dateTime)
                ;

            return $workout;
        });

        $manager->flush();
    }
}

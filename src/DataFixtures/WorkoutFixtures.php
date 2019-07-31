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
                ->setDuration($this->faker->dateTime);// faker->time string

            return $workout;
        });


        $manager->flush();
    }
}

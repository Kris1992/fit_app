<?php

namespace App\DataFixtures;

use App\Entity\Workout;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class WorkoutFixtures extends BaseFixture implements DependentFixtureInterface
{

    private static $activities = [
        'running_activity',
        'cycling_activity'
    ];

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            MovementActivityFixtures::class,
            MovementSetActivityFixtures::class,
        ];
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'movement_workout', function($i) 
        {
            $workout = new Workout();
            $workout
                ->setUser($this->getRandomReference('main_users'))
                ->setActivity($this->getRandomReference($this->faker->randomElement(self::$activities)))
                ->setDurationSecondsTotal($this->faker->numberBetween($min = 1, $max = 86399))
                /* max time -> 23:59:59 */ 
                ->calculateSaveDistanceTotal()
                ->calculateSaveBurnoutEnergyTotal()
                ->setStartAt($this->faker->dateTime)
                ;

            return $workout;
        });

        $manager->flush();
    }
}

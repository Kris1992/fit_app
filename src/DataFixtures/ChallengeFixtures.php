<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Challenge;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ChallengeFixtures extends BaseFixture implements DependentFixtureInterface
{
    private static $movementActivities = [
        'running_activity',
        'cycling_activity',
    ];

    private static $goalsArray = [ 
            'durationSecondsTotal',
            'burnoutEnergyTotal',
            //'distanceTotal',
            //'repetitionsTotal'
    ];

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            MovementActivityFixtures::class,
        ];
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'main_challenges', function($i)
        {
            $activity = $this->getRandomReference($this->faker->randomElement(self::$movementActivities));

            $challenge = new Challenge();
            $challenge
                ->setTitle($this->faker->sentence($nbWords = 6, $variableNbWords = true))
                ->setActivityName($activity->getName())
                ->setActivityType($activity->getType())
                ->setGoalProperty($this->faker->randomElement(self::$goalsArray))
                ->creationTimeStamp()
                ->setStartAt($this->faker->dateTimeBetween('+0 days', '+1 month'))
                ->setStopAt($this->faker->dateTimeBetween('+1 month', '+3 month'))
                ;

            for($index = 0; $index < 6; $index++) {
                $challenge
                    ->addParticipant($this->getReferenceByIndex('main_users', $index))
                ;
            }

            return $challenge;
        });

        $manager->flush();
    }

}

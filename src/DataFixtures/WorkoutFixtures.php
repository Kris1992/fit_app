<?php

namespace App\DataFixtures;

use App\Entity\Workout;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Services\ImagesManager\ImagesManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

class WorkoutFixtures extends BaseFixture implements DependentFixtureInterface
{

    private static $movementActivities = [
        'running_activity',
        'cycling_activity',
    ];

    private static $bodyweightActivities = [
        'pumpup_activity',
    ];

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            MovementActivityFixtures::class,
            MovementSetActivityFixtures::class,
            BodyweightActivityFixtures::class,
        ];
    }

    private $workoutsImagesManager;

    public function __construct(ImagesManagerInterface $workoutsImagesManager)
    {
        $this->workoutsImagesManager = $workoutsImagesManager;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'movement_workout', function($i) 
        {
            $workout = new Workout();
            $workout
                ->setUser($this->getRandomReference('main_users'))
                ->setActivity($this->getRandomReference($this->faker->randomElement(self::$movementActivities)))
                ->setDurationSecondsTotal($this->faker->numberBetween($min = 1, $max = 86399))
                /* max time -> 23:59:59 */ 
                ->calculateSaveDistanceTotal()
                ->calculateSaveBurnoutEnergyTotal()
                ->setStartAt($this->faker->dateTime)
                ;

            //In test env we do need waste of time to upload images
            if ($_ENV['APP_ENV'] !== 'test') {
                $imageFilename = $this->uploadFakeImage($workout->getUser()->getLogin());
                $workout
                    ->setImageFilename($imageFilename)
                    ;
            }

            return $workout;
        });

        $this->createMany(10, 'bodyweight_workout', function($i) 
        {   
            $activity = $this->getRandomReference($this->faker->randomElement(self::$bodyweightActivities));

            $workout = new Workout();
            $workout
                ->setUser($this->getRandomReference('main_users'))
                ->setActivity($activity)
                ->setDurationSecondsTotal($this->faker->numberBetween($min = 1, $max = 86399))
                /* max time -> 23:59:59 */ 
                ->setRepetitionsTotal($activity->getRepetitionsAvgMin())
                ->calculateSaveBurnoutEnergyTotal()
                ->setStartAt($this->faker->dateTime)
                ;

            return $workout;
        });

        $manager->flush();
    }

    private function uploadFakeImage(string $subdirectory): string
    {
        $randomImage = 'image'.$this->faker->numberBetween(0, 3).'.jpg';
        $imagePath = __DIR__.'/workout_images/'.$randomImage;

        return $this->workoutsImagesManager
            ->uploadImage(new File($imagePath), null, $subdirectory)
            ;
    }
}

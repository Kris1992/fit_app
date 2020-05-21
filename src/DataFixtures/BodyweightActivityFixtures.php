<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\BodyweightActivity;
use Doctrine\Common\Persistence\ObjectManager;

class BodyweightActivityFixtures extends BaseFixture
{

	private static $bodyweightActivities = [
        'Push-ups',
    ];

    private static $bodyweightIntensities = [
        [
            'name' => 'Very low',
            'min' => 40,
            'max' => 60,
            'energy' => [
                'min' => 300,
                'max' => 350
            ],
        ],
        [
            'name' => 'Low',
            'min' => 61,
            'max' => 90,
            'energy' => [
                'min' => 351,
                'max' => 400
            ],
        ],
        [
            'name' => 'Normal',
            'min' => 91,
            'max' => 110,
            'energy' => [
                'min' => 401,
                'max' => 450
            ],
        ],
        [
            'name' => 'High',
            'min' => 111,
            'max' => 130,
            'energy' => [
                'min' => 451,
                'max' => 500
            ],
        ],
        [
            'name' => 'Very high',
            'min' => 131,
            'max' => 150,
            'energy' => [
                'min' => 501,
                'max' => 550
            ],
        ]
    ];

    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(5, 'pumpup_activity', function($i)
        {
            $activity = new BodyweightActivity();
            $activity
                ->setType('Bodyweight')
                ->setName(self::$bodyweightActivities[0])
                ->setIntensity(self::$bodyweightIntensities[$i]['name'])
                ->setEnergy(
                    $this->faker->numberBetween(
                        self::$bodyweightIntensities[$i]['energy']['min'],
                        self::$bodyweightIntensities[$i]['energy']['max']
                    )
                ) 
                ->setRepetitionsAvgMin(self::$bodyweightIntensities[$i]['min'])
                ->setRepetitionsAvgMax(self::$bodyweightIntensities[$i]['max'])
                ; 

            return $activity;
        });

        $manager->flush();
    }
}

<?php
declare(strict_types=1);

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
        [
            'name' => 'Very slow',
            'min' => 4,//4,
            'max' => 10,//6,
            'energy' => [
                'min' => 650,
                'max' => 750
            ],
        ],
        [
            'name' => 'Slow',
            'min' => 11,//7,
            'max' => 20,//9,
            'energy' => [
                'min' => 751,
                'max' => 900
            ],
        ],
        [
            'name' => 'Normal',
            'min' => 21,//10,
            'max' => 30,//12,
            'energy' => [
                'min' => 901,
                'max' => 1000
            ],
        ],
        [
            'name' => 'Fast',
            'min' => 31,//13,
            'max' => 40,//15,
            'energy' => [
                'min' => 1001,
                'max' => 1200
            ],
        ],
        [
            'name' => 'Very fast',
            'min' => 41,//16,
            'max' => 50,//18,
            'energy' => [
                'min' => 1201,
                'max' => 1300
            ],
        ]
    ];

    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(5, 'running_activity', function($i)
        {
            $activity = new MovementActivity();
            $activity
                ->setType('Movement')
                ->setName(self::$movementActivities[0])
                ->setIntensity(self::$movementIntensities[$i]['name'])
                ->setEnergy(
                    $this->faker->numberBetween(
                        self::$movementIntensities[$i]['energy']['min'],
                        self::$movementIntensities[$i]['energy']['max']
                    )
                ) 
                ->setSpeedAverageMin(self::$movementIntensities[$i]['min'])
                ->setSpeedAverageMax(self::$movementIntensities[$i]['max'])
                ;

            return $activity;
        });

        $this->createMany(5, 'cycling_activity', function($i)
        {
            $activity = new MovementActivity();
            $activity
                ->setType('Movement')
                ->setName(self::$movementActivities[1])
                ->setIntensity(self::$movementIntensities[$i]['name'])
                ->setEnergy(
                    $this->faker->numberBetween(
                        self::$movementIntensities[$i]['energy']['min'],
                        self::$movementIntensities[$i]['energy']['max']
                    )
                ) 
                ->setSpeedAverageMin(self::$movementIntensities[$i]['min'])
                ->setSpeedAverageMax(self::$movementIntensities[$i]['max'])
                ;

            return $activity;
        });

        $manager->flush();
    }
}

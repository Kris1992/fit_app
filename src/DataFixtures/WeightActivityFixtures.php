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

    private static $weightRepetitionsOptions = [
        [
            'min' => 1,
            'max' => 50,
            'energy' => [
                'min' => 100,
                'max' => 200
            ],
        ],
        [
            'min' => 51,
            'max' => 100,
            'energy' => [
                'min' => 201,
                'max' => 300
            ],
        ],
        [
            'min' => 101,
            'max' => 300,
            'energy' => [
                'min' => 301,
                'max' => 400
            ],
        ],
    ];

    private static $weightLoadOptions = [
        [
            'min' => 1,
            'max' => 50
        ],
        [
            'min' => 51,
            'max' => 120
        ],
        [
            'min' => 121,
            'max' => 300
        ],
    ];





/*
    private static $weightActivityOptions = 
    [   
        [
            'repetitionsAvg' => [
                'min' => 1,
                'max' => 50,
            ],
            'weightAvg' => [
                'min' => 1,
                'max' => 50
            ],
            'energy' => [
                'min' => 100,
                'max' => 200
            ],
        ],
        [
            'repetitionsAvg' => [
                'min' => 51,
                'max' => 100,
            ],
            'weightAvg' => [
                'min' => 51,
                'max' => 120
            ],
            'energy' => [
                'min' => 201,
                'max' => 200
            ],
        ],
        [
            'repetitionsAvg' => [
                'min' => 101,
                'max' => 300,
            ],
            'weightAvg' => [
                'min' => 121,
                'max' => 300
            ],
            'energy' => [
                'min' => 301,
                'max' => 400
            ],
        ],
    ];*/



    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(9, 'weight_activity_0', function($i)
        {
            $index = $i; 
            $j = intval($i/3);
            if (($i+1) >= 3) {
                $index = $i - ($j*3);
            }       

            $activity = new WeightActivity();
            $activity
                ->setType('Weight')
                ->setName(self::$weightActivities[0])
                ->setEnergy(
                    $this->faker->numberBetween(
                        self::$weightRepetitionsOptions[$index]['energy']['min'],
                        self::$weightRepetitionsOptions[$index]['energy']['max']
                    )
                ) 
                ->setRepetitionsAvgMin(self::$weightRepetitionsOptions[$index]['min'])
                ->setRepetitionsAvgMax(self::$weightRepetitionsOptions[$index]['max'])
                ->setWeightAvgMin(self::$weightLoadOptions[$j]['min'])
                ->setWeightAvgMax(self::$weightLoadOptions[$j]['max'])
                ; 

            return $activity;
        });

        $manager->flush();
    }
}



                

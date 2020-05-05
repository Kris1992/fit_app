<?php

namespace App\DataFixtures;

use App\Entity\Curiosity;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Services\ImagesManager\ImagesManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

class CuriosityFixtures extends BaseFixture implements DependentFixtureInterface
{

    public function getDependencies()
    {
        //I try to resolve problem with construct UserFixtures
        //if ($_ENV['APP_ENV'] !== 'test') {
            return [
                UserFixtures::class,
            ];
        
    }

    public function __construct()//ImagesManagerInterface $workoutsImagesManager)
    {
        //$this->workoutsImagesManager = $workoutsImagesManager;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'admins_curiosities', function($i) 
        {
            $curiosity = new Curiosity();
            $curiosity
                ->setAuthor($this->getRandomReference('admin_users'))
                ->setTitle($this->faker->sentence($nbWords = 6, $variableNbWords = true))
                ->setDescription($this->faker->text($maxNbChars = 40))
                ->setContent($this->faker->realText())
                ->setCreatedAt($this->faker->dateTimeBetween('-200 days', '-100 days'))
                ;

            if ($this->faker->boolean(70)) {
                $curiosity
                    ->publish()
                    ;
            }
            //In test env we do need waste of time to upload images
            //if ($_ENV['APP_ENV'] !== 'test') {
            //    $imageFilename = $this->uploadFakeImage($workout->getUser()->getLogin());
            //    $workout
            //        ->setImageFilename($imageFilename)
            //        ;
           // }

            return $curiosity;
        });

        $manager->flush();
    }
/*
    private function uploadFakeImage(string $subdirectory): string
    {
        $randomImage = 'image'.$this->faker->numberBetween(0, 3).'.jpg';
        $imagePath = __DIR__.'/workout_images/'.$randomImage;

        return $this->workoutsImagesManager
            ->uploadImage(new File($imagePath), null, $subdirectory)
            ;
    }
    */
}

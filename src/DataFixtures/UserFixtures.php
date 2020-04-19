<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Services\ImagesManager\ImagesManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

class UserFixtures extends BaseFixture
{

	private $passwordEncoder;
    private $userImageManager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, ImagesManagerInterface
        $userImageManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userImageManager = $userImageManager;
    }

    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(10, 'main_users', function($i)
        {
            $user = new User();
            $user
                ->setEmail(sprintf('user%d@fit.com', $i)) 
                ->setFirstName($this->faker->firstName)
                ->setSecondName($this->faker->lastName)
                ->saveLogin()
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    'krakowdev01'
                ))
                ->setBirthdate($this->faker->dateTime) //$this->faker->date);
                ->setGender($this->faker->randomElement($array = array ('Male','Female')))
                ->setWeight($this->faker->numberBetween($min = 45, $max = 120))
                ->setHeight($this->faker->numberBetween($min = 100, $max = 200))
                ->agreeToTerms()
                ; 

            //In test env we do need waste of time to upload images
            if ($_ENV['APP_ENV'] !== 'test') {
                $imageFilename = $this->uploadFakeImage($user->getLogin());
                $user
                    ->setImageFilename($imageFilename)
                    ;
            }

            return $user;
        });

        //admins
        
        $this->createMany(3, 'admin_users', function($i) {
            $user = new User();
            $user
                ->setEmail(sprintf('admin%d@fit.com', $i))
                ->setFirstName($this->faker->firstName)
                ->setSecondName($this->faker->lastName)
                ->setRoles(['ROLE_ADMIN'])
                ->saveLogin()
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    'admin01'
                ))
                ->setBirthdate($this->faker->dateTime) //$this->faker->date);
                ->setGender($this->faker->randomElement($array = array ('Male','Female')))
                ->setWeight($this->faker->numberBetween($min = 45, $max = 120))
                ->setHeight($this->faker->numberBetween($min = 100, $max = 200))
                ->agreeToTerms()
                ;
            
            return $user;
        });


        $manager->flush();
    }

    private function uploadFakeImage(string $subdirectory): string
    {
        $randomImage = 'image'.$this->faker->numberBetween(0, 3).'.jpg';
        $imagePath = __DIR__.'/user_images/'.$randomImage;

        return $this->userImageManager
            ->uploadImage(new File($imagePath), null, $subdirectory)
            ;
    }
}

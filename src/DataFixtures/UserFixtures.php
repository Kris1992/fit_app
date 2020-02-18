<?php

namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixture
{

	private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(10, 'main_users', function($i)
        {
            $user = new User();
            $user->setEmail(sprintf('user%d@fit.com', $i)); 
            $user->setFirstName($this->faker->firstName);
            $user->setSecondName($this->faker->lastName);
			$user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'krakowdev01'
            ));
            $user->setBirthdate($this->faker->dateTime);//$this->faker->date);
            $user->setGender($this->faker->randomElement($array = array ('Male','Female')));
            $user->setWeight($this->faker->numberBetween($min = 45, $max = 120));
            $user->setHeight($this->faker->numberBetween($min = 100, $max = 200));

            $user->agreeToTerms(); 

            return $user;
        });


        //admins
        
        $this->createMany(3, 'admin_users', function($i) {
            $user = new User();
            $user->setEmail(sprintf('admin%d@fit.com', $i));
            $user->setFirstName($this->faker->firstName);
            $user->setSecondName($this->faker->lastName);
            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'admin01'
            ));
            $user->setBirthdate($this->faker->dateTime);//$this->faker->date);
            $user->setGender($this->faker->randomElement($array = array ('Male','Female')));
            $user->setWeight($this->faker->numberBetween($min = 45, $max = 120));
            $user->setHeight($this->faker->numberBetween($min = 100, $max = 200));
            $user->agreeToTerms();
            
            return $user;
        });


        $manager->flush();
    }
}

<?php
namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

use App\Validator\UniqueUser;
use App\Validator\ContainsAlphanumeric;



class UserRegistrationFormModel
{
	/**
     * @Assert\NotBlank(message="Please enter an email")
     * @Assert\Email()
     * @UniqueUser()
     */
    public $email;

     /**
     * @Assert\NotBlank(message="Please enter your first name!")
     */
    public $firstName;

    /**
     * @Assert\NotBlank(message="Please enter your second name!")
     */
    public $secondName;

    /**
     * @Assert\NotBlank(message="Choose a password!")
     * @ContainsAlphanumeric()
     */
    public $plainPassword;

    /**
     * @Assert\IsTrue(message="You must agree to our terms.")
     */
    public $agreeTerms;

  
}

?>
<?php
namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
//use App\Validator\UniqueUser;// moj walidator
// @UniqueUser()// tylko email trafia do walidacji 


class UserRegistrationFormModel
{
	/**
     * @Assert\NotBlank(message="Please enter an email")
     * @Assert\Email()
     */
    public $email;

     /**
     * @Assert\NotBlank(message="Please enter your name!")
     */
    public $firstName;

    /**
     * @Assert\NotBlank(message="Choose a password!")
     * @Assert\Length(min=5, minMessage="Password must contain minimum 5 letters!")
     */
    public $plainPassword;

  
}

?>
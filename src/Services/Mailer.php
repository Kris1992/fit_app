<?php
namespace App\Services;
 
use Symfony\Component\HttpFoundation\Response;

class Mailer
{
    private $swiftMailer;
    private $template;

    /**
     * Mailer Constructor
     *
     *@param Swift_Mailer $mailer
     *@param Twig_Environment $template
     */
    public function __construct(\Swift_Mailer $swiftMailer,  \Twig_Environment $template)  
    {
        
        $this->swiftMailer = $swiftMailer;
        $this->template = $template;
    }

    public function sendPassword($name, $email, $token)
    {
        //send reset password mail
        $message = (new \Swift_Message('Reset password!'))
            ->setFrom('krakowdev01@gmail.com')
            ->setTo($email)
            ->setBody(
                $this->template->render(
                    'emails/password_email.html.twig',
                    [   
                        'name' => $name,
                        'token' => $token
                    ]
                ),
                'text/html'
            );
            $this->swiftMailer->send($message);
    }

}


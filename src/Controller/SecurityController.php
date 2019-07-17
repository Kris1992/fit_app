<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

//register
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\LoginFormAuthenticator;
use App\Form\UserRegistrationFormType;
use App\Form\Model\UserRegistrationFormModel;




class SecurityController extends AbstractController
{
	
	/**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
		$error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator)
    {


        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $userModel = new UserRegistrationFormModel();
            $userModel = $form->getData();

            $user = new User();
            $user->setEmail($userModel->email);
            $user->setFirstName($userModel->firstName);

            
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $userModel->plainPassword
            ));
            // be absolutely sure they agree
            //if (true === $userModel->agreeTerms)
            //{
            //$form['agreeTerms']->getData()) {// jeszcze raz walidujemy dla pewności
            //    $user->agreeToTerms();
            //}

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $formAuthenticator,
                'main' // firewall name
            );
        }


        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
//register
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\HttpFoundation\Request;
use App\Security\LoginFormAuthenticator;
use App\Form\UserRegistrationFormType;
use App\Entity\User;
use App\Services\UserRegister\UserRegistrationInterface;

class SecurityController extends AbstractController
{
	
	/**
     * @Route("/login", name="app_login", methods={"POST", "GET"})
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
     * @Route("/logout", name="app_logout",  methods={"GET"})
     */
    public function logout()
    {

    }

    /**
     * @Route("/register", name="app_register", methods={"POST", "GET"})
     */
    public function register(Request $request, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator, UserRegistrationInterface $userRegistration)
    {

        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            /** @var UserRegistrationFormModel $userModel */
            $userModel = $form->getData();

            try {
                $user = $userRegistration->register(
                            $request, 
                            $userModel, 
                            $form['imageFile']->getData()
                        );
            } catch (\Exception $e) {
                return $this->render('security/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'ReCaptchaError' => $e->getMessage()
                ]);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

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

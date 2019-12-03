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

use App\Services\UploadImagesHelper;

use ReCaptcha\ReCaptcha;




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
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator, UploadImagesHelper $uploadImagesHelper, string $secret_key)
    {

        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

                $isHuman = $this->checkCatchpa($request, $secret_key);
                if ($isHuman->isSuccess()) {

                    $userModel = new UserRegistrationFormModel();
                    $userModel = $form->getData();

                    $user = new User();
                    $user->setEmail($userModel->getEmail());
                    $user->setFirstName($userModel->getFirstName());
                    $user->setSecondName($userModel->getSecondName());
                    $user->setGender($userModel->getGender());
                    $user->setRoles(['ROLE_USER']);


                    /** @var UploadedFile $uploadedFile */
                    $uploadedFile = $form['imageFile']->getData();

                    if($uploadedFile)
                    {
                        $newFilename = $uploadImagesHelper->uploadUserImage($uploadedFile, null);

                        $user->setImageFilename($newFilename);
                    }

            
                    $user->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $userModel->getPlainPassword()
                    ));
            
                    if (true === $userModel->getAgreeTerms())//to make sure it's valid data
                    {
                        $user->agreeToTerms();
                    }

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
                    else {
                        //$errors = $isHuman->getErrorCodes();
                        $message = 'The ReCaptcha was not entered correctly!';
                        
                        return $this->render('security/register.html.twig', [
                            'registrationForm' => $form->createView(),
                            'ReCaptchaError' => $message
                        ]);
                    }
        }


        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function checkCatchpa(Request $request, string $secret_key)
    {
        $recaptcha = new ReCaptcha($secret_key);
        return $isHuman = $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
                ->verify($request->get('g-recaptcha-response'), $_SERVER['REMOTE_ADDR']);

    }

}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use App\Repository\UserRepository;
use App\Repository\PasswordTokenRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Services\Mailer;

use Doctrine\ORM\EntityManagerInterface;
use App\Form\RenewPasswordFormType;
use App\Form\Model\RenewPasswordFormModel;

use App\Entity\PasswordToken;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\LoginFormAuthenticator;



use App\Form\UserRegistrationFormType;
use App\Form\Model\UserRegistrationFormModel;
use App\Services\UploadImagesHelper;


class AccountController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     */
    public function profile()
    {
        return $this->render('account/profile.html.twig', [
           
        ]);
    }

    /**
     * @Route("/account/edit", name="account_edit", methods={"POST", "GET"})
     */
    public function edit(Request $request, EntityManagerInterface $em, UserRegistrationFormModel $userModel, UploadImagesHelper $uploadImagesHelper)
    {
        
        //if ($user != $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {//change to voter soon
        //    throw $this->createAccessDeniedException('No access!');
        //}

        /** @var User $user */
        $user = $this->getUser();

        //transform user object to userModel object
        $userModel->setId($user->getId());
        $userModel->setEmail($user->getEmail());
        $userModel->setFirstName($user->getFirstName());
        $userModel->setSecondName($user->getSecondName());
        if($user->getImageFilename())
        {
            $userModel->setImageFilename($user->getImageFilename());
        }
        
            
        $form = $this->createForm(UserRegistrationFormType::class, $userModel);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {

            $userModel2 = $form->getData();
            
            $user->setEmail($userModel2->getEmail());
            $user->setFirstName($userModel2->getFirstName());
            $user->setSecondName($userModel2->getSecondName());

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();

            if($uploadedFile)
            {
                $newFilename = $uploadImagesHelper->uploadUserImage($uploadedFile, $user->getImageFilename());
                $user->setImageFilename($newFilename);
            }

            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Your account is updated!');

            return $this->redirectToRoute('account_edit');
        }
        return $this->render('account/edit.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/reset", name="app_reset_password")
     */
    public function resetPassword(Request $request, CsrfTokenManagerInterface $csrfTokenManager, UserRepository $userRepository, Mailer $mail, EntityManagerInterface $em)
    {

    	if($request->isMethod('POST'))
    	{
    		$formData = [
    			'email' => $request->request->get('email'),
        		'csrf_token' => $request->request->get('_csrf_token')
        	];

        	$token = new CsrfToken('authenticate', $formData['csrf_token']);
        	if (!$csrfTokenManager->isTokenValid($token)) {
            	throw new InvalidCsrfTokenException();
        	}

	        $user = $userRepository->findOneBy(['email' => $formData['email']]);
        	
        	if (!$user) {
        		$this->addFlash('warning', 'E-mail not found in database!');	
        	}
        	else
        	{
        		$passTokenOld = $user->getPasswordToken();
        		$passToken = new PasswordToken($user);
        		$user->setPasswordToken($passToken);
            	$em->persist($passToken);
            	$em->persist($user);
            	if($passTokenOld)
            	{
            		$em->remove($passTokenOld);
            	}
            	$em->flush();

        		$mail->sendPassword($user->getFirstName(), $formData['email'], $passToken->getToken()); 

        		$this->addFlash('success', 'Check your email! I send message to you');
        	}
    	}

        return $this->render('account/reset_password.html.twig');
    }

    /**
     * @Route("/password/renew/{token}", name="app_renew_password")
     */
    public function renewPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, $token, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator, PasswordToken $token1)
    {

    	if(!$token1 || $token1->isExpired())
    	{
    		$this->addFlash('warning', 'Reset password token not exist or expired!');

    		return $this->redirectToRoute('app_reset_password');
    	}
    	

    	$form = $this->createForm(RenewPasswordFormType::class);
    	$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $passwordModel = new RenewPasswordFormModel();
            $passwordModel = $form->getData();

            $user = $token1->getUser();

            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $passwordModel->getPlainPassword()
            ));

            $user->setPasswordToken(null);
            $user->resetFailedAttempts();
            $em->persist($user);
            //$em->flush();
            //$em->remove($token1);
            $em->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $formAuthenticator,
                'main' // firewall name
            );

        }

        return $this->render('account/renew_password.html.twig', [
            'renewPasswordForm' => $form->createView(),
        ]);
    }

}

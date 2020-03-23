<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use App\Repository\UserRepository;
use App\Repository\PasswordTokenRepository;
use App\Repository\WorkoutRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Services\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RenewPasswordFormType;
use App\Form\Model\RenewPasswordFormModel;
use App\Entity\PasswordToken;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\LoginFormAuthenticator;
use App\Form\UserRegistrationFormType;
use App\Form\Model\User\UserRegistrationFormModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Services\ImagesManager\ImagesManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\Command\DeleteUserImage;


class AccountController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     * @IsGranted("ROLE_USER")
    */
    public function profile(WorkoutRepository $workoutRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        $personalBest = $workoutRepository->getHighScoresByUser($user);

        $workouts = $workoutRepository->getLastWorkoutsByUser($user, 3);
        $totalData = $workoutRepository->getWorkoutsTimeAndNumWorkoutsByUser($user);

        return $this->render('account/profile.html.twig', [
            'workouts' => $workouts,
            'totalData' => $totalData,
            'personalBest' => $personalBest
        ]);
    }

    /**
     * @Route("/account/edit", name="account_edit", methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, EntityManagerInterface $em, UserRegistrationFormModel $userModel, ImagesManagerInterface $ImagesManager)
    {
        /** @var User $user */
        $user = $this->getUser();

        //transform user object to userModel object
        $userModel->setId($user->getId());
        $userModel->setEmail($user->getEmail());
        $userModel->setFirstName($user->getFirstName());
        $userModel->setSecondName($user->getSecondName());
        $userModel->setGender($user->getGender());
        $userModel->setBirthdate($user->getBirthdate());
        $userModel->setWeight($user->getWeight());
        $userModel->setHeight($user->getHeight());
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
            $user->setGender($userModel2->getGender());
            $user->setBirthdate($userModel2->getBirthdate());
            $user->setWeight($userModel2->getWeight());
            $user->setHeight($userModel2->getHeight());

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();

            if($uploadedFile)
            {
                $newFilename = $ImagesManager->uploadUserImage($uploadedFile, $user->getImageFilename());
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

    //API

    /**
     *
     * @Route("/api/account/delete_user_image", name="api_delete_user_image",
     * methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function deleteUserImageAction(Request $request, ImagesManagerInterface $ImagesManager, MessageBusInterface $messageBus): Response
    {

        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if($data === null) {
            throw new BadRequestHttpException('Invalid Json');    
        }

        $userId = $user->getId();

        if($userId == $data['userId']) {
            $imageFilename = $user->getImageFilename();
            if(!empty($imageFilename)) {
                $messageBus->dispatch(new DeleteUserImage($userId));
                return new JsonResponse(Response::HTTP_OK);
            }
        }

        $responseMessage = [
            'errorMessage' => 'Image not found!'
        ];

        return new JsonResponse($responseMessage, Response::HTTP_BAD_REQUEST);
    }

}

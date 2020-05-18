<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Exception\Api\ApiBadRequestHttpException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use App\Repository\UserRepository;
use App\Repository\PasswordTokenRepository;
use App\Repository\WorkoutRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RenewPasswordFormType;
use App\Form\Model\User\RenewPasswordFormModel;
use App\Entity\PasswordToken;
use App\Entity\User;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Security\LoginFormAuthenticator;
use App\Form\UserRegistrationFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\Command\DeleteUserImage;
use App\Services\Factory\UserModel\UserModelFactoryInterface;
use App\Services\Updater\User\UserUpdaterInterface;
use App\Services\Mailer\MailingSystemInterface;
use App\Services\JsonErrorResponse\JsonErrorResponse;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;

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
        $likes = $workoutRepository->countAllWorkoutsReactionsByUserAndType($user, 1);

        return $this->render('account/profile.html.twig', [
            'workouts' => $workouts,
            'totalData' => $totalData,
            'personalBest' => $personalBest,
            'totalLikes' => $likes
        ]);
    }

    /**
     * @Route("/account/{id}/show", name="account_show")
     * @IsGranted("ROLE_USER")
    */
    public function show(User $user, WorkoutRepository $workoutRepository)
    {
        $personalBest = $workoutRepository->getHighScoresByUser($user);

        $workouts = $workoutRepository->getLastWorkoutsByUser($user, 3);
        $totalData = $workoutRepository->getWorkoutsTimeAndNumWorkoutsByUser($user);
        $likes = $workoutRepository->countAllWorkoutsReactionsByUserAndType($user, 1);

        return $this->render('account/show.html.twig', [
            'user' => $user,
            'workouts' => $workouts,
            'totalData' => $totalData,
            'personalBest' => $personalBest,
            'totalLikes' => $likes
        ]);
    }

    /**
     * @Route("/account/edit", name="account_edit", methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, UserModelFactoryInterface $userModelFactory, UserUpdaterInterface $userUpdater)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var UserRegistrationFormModel $userModel */
        $userModel = $userModelFactory->create($user);
            
        $form = $this->createForm(UserRegistrationFormType::class, $userModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userUpdater->update($userModel, $user, $form['imageFile']->getData());

            $entityManager->flush();
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
    public function resetPassword(Request $request, CsrfTokenManagerInterface $csrfTokenManager, UserRepository $userRepository, MailingSystemInterface $mailer, EntityManagerInterface $entityManager)
    {
    	if($request->isMethod('POST')) {
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
        	} else {
        		$passTokenOld = $user->getPasswordToken();
        		$passToken = new PasswordToken($user);
        		$user->setPasswordToken($passToken);
            	$entityManager->persist($passToken);
            	$entityManager->persist($user);
            	if($passTokenOld) {
            		$entityManager->remove($passTokenOld);
            	}
            	$entityManager->flush();

                $mailer->sendResetPasswordMessage($user);
        		$this->addFlash('success', 'Check your email! We send message to you.');
        	}
    	}

        return $this->render('account/reset_password.html.twig');
    }

    /**
     * @Route("/password/renew/{token}", name="app_renew_password")
     */
    public function renewPassword($token, Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator, PasswordToken $passwordToken)
    {

    	if(!$passwordToken || $passwordToken->isExpired()) {
    		$this->addFlash('warning', 'Reset password token not exist or expired!');

    		return $this->redirectToRoute('app_reset_password');
    	}
    	
    	$form = $this->createForm(RenewPasswordFormType::class);
    	$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $passwordModel = $form->getData();
            $user = $passwordToken->getUser();

            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $passwordModel->getPlainPassword()
            ));

            $user->setPasswordToken(null);
            $user->resetFailedAttempts();
            //$em->flush();
            //$em->remove($passwordToken);
            $entityManager->flush();

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
     * @Route("/api/account/delete_user_image", name="api_delete_user_image", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function deleteUserImageAction(Request $request, MessageBusInterface $messageBus, JsonErrorResponseFactory $jsonErrorFactory): Response
    {

        /** @var User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $userId = $user->getId();

        if($userId == $data['id']) {
            $imageFilename = $user->getImageFilename();
            if(!empty($imageFilename)) {
                $messageBus->dispatch(new DeleteUserImage($userId));
                return new JsonResponse(Response::HTTP_OK);
            }
        }

        $jsonError = new JsonErrorResponse(404, 
            JsonErrorResponse::TYPE_NOT_FOUND_ERROR,
            'Image not found.');

        return $jsonErrorFactory->createResponse($jsonError);
    }

}

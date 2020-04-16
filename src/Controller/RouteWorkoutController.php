<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Form\WorkoutWithMapFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Services\Factory\Workout\WorkoutFactory;
use App\Services\ModelValidator\ModelValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\ModelExtender\WorkoutSpecificExtender;
use Symfony\Component\Form\FormInterface;

class RouteWorkoutController extends AbstractController
{

     /**
     * @Route("route/workout/draw_route", name="route_workout_draw_route", methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function drawRoute(string $map_api_key)
    {
        $form = $this->createForm(WorkoutWithMapFormType::class);

        return $this->render('route_workout/add_drawed.html.twig', [
            'map_api_key' => $map_api_key,
            'workoutForm' => $form->createView()
        ]);
    }

    /**
     * @Route("api/route/workout/add_drawed", name="api_route_workout_add_drawed")
     */
    public function addDrawedAction(Request $request, WorkoutSpecificExtender $workoutSpecificExtender, EntityManagerInterface $em, ModelValidatorInterface $modelValidator)
    {
        $data = json_decode($request->getContent(), true);

        if($data === null) {
            throw new BadRequestHttpException('Invalid Json');    
        }

        $form = $this->createForm(WorkoutWithMapFormType::class);
        $form->submit($data['formData']);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        $workoutSpecificModel = $form->getData();

        $workoutSpecificModel = $workoutSpecificExtender->fillWorkoutModelWithMap($workoutSpecificModel, $user, $data);

        if ($workoutSpecificModel) {
            $isValid = $modelValidator->isValid($workoutSpecificModel, ['route_model']);

            if ($isValid) {
                try {
                    $workoutFactory = WorkoutFactory::chooseFactory($workoutSpecificModel->getType());
                    $workout = $workoutFactory->create($workoutSpecificModel);

                    $em->persist($workout);
                    $em->flush();
                    
                    $response = [
                        'url' => $this->generateUrl(
                                    'workout_list', 
                                    [], 
                                    UrlGeneratorInterface::ABSOLUTE_URL
                                )
                    ];

                    $this->addFlash('success', 'Workout was created!!');

                    return new JsonResponse($response, Response::HTTP_OK);
                } catch (\Exception $e) {
                    $responseMessage = [
                        'errorMessage' => $e->getMessage()
                    ];

                    return new JsonResponse($responseMessage, Response::HTTP_BAD_REQUEST);
                }

            } else {
                $errors = $modelValidator->getErrors();

                //Return just first one error
                $responseMessage = [
                        'errorMessage' => $errors[0]->getMessage()
                ];

                return new JsonResponse($responseMessage, Response::HTTP_BAD_REQUEST);
            }
        }        
    
        $responseMessage = [
            'errorMessage' => 'Cannot add workout. Check data and try again'
        ];

        return new JsonResponse($responseMessage, Response::HTTP_BAD_REQUEST);
    }

    

    protected function getErrorsFromForm(FormInterface $form)
    {
        foreach ($form->getErrors() as $error) {
            return $error->getMessage();
        }

        $errors = array();
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childError = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childError;
                }
            }
        }

        return $errors;
    }
}

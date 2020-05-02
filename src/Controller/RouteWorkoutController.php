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
use App\Exception\Api\ApiBadRequestHttpException;
use App\Services\JsonErrorResponse\JsonErrorResponse;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;

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
    public function addDrawedAction(Request $request, WorkoutSpecificExtender $workoutSpecificExtender, EntityManagerInterface $em, ModelValidatorInterface $modelValidator, JsonErrorResponseFactory $jsonErrorFactory)
    {
        $data = json_decode($request->getContent(), true);
        if($data === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $form = $this->createForm(WorkoutWithMapFormType::class);
        $form->submit($data['formData']);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);
            $jsonError = new JsonErrorResponse(400, 
                JsonErrorResponse::TYPE_FORM_VALIDATION_ERROR,
                null
                );

            $jsonError->setArrayExtraData($errors);
            return $jsonErrorFactory->createResponse($jsonError);
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
                                    'workout_report', 
                                    [
                                        'id' => $workout->getId()
                                    ], 
                                    UrlGeneratorInterface::ABSOLUTE_URL
                                )
                    ];

                    $this->addFlash('success', 'Workout was created!!');

                    return new JsonResponse($response, Response::HTTP_OK);
                } catch (\Exception $e) {
                    $jsonError = new JsonErrorResponse(400, 
                        JsonErrorResponse::TYPE_ACTION_FAILED,
                        $e->getMessage()
                    );

                    return $jsonErrorFactory->createResponse($jsonError);
                }

            } else {
                $jsonError = new JsonErrorResponse(400, 
                    JsonErrorResponse::TYPE_ACTION_FAILED,
                    $modelValidator->getErrorMessage()
                );

                return $jsonErrorFactory->createResponse($jsonError);
            }
        }        
        
        $jsonError = new JsonErrorResponse(400, 
            JsonErrorResponse::TYPE_ACTION_FAILED,
            'Cannot add workout. Please check data and try again.'
        );

        return $jsonErrorFactory->createResponse($jsonError);
    }

    /**
     * @Route("route/workout/track_route", name="route_workout_track_route", methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function trackRoute(string $map_api_key)
    {
        $form = $this->createForm(WorkoutWithMapFormType::class, null, [ 'is_drawing' => false ]);

        return $this->render('route_workout/track_route.html.twig', [
            'map_api_key' => $map_api_key,
            'workoutForm' => $form->createView()
        ]);
    }

    /**
     * @Route("api/route/workout/add_tracked", name="api_route_workout_add_tracked")
     */
    public function addTrackedAction(Request $request, WorkoutSpecificExtender $workoutSpecificExtender, EntityManagerInterface $em, ModelValidatorInterface $modelValidator, JsonErrorResponseFactory $jsonErrorFactory)
    {
        $data = json_decode($request->getContent(), true);

        dump($data);
        if($data === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $form = $this->createForm(WorkoutWithMapFormType::class, null, [ 'is_drawing' => false ]);
        $form->submit($data['formData']);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);// FormApiValidator
            $jsonError = new JsonErrorResponse(400, 
                JsonErrorResponse::TYPE_FORM_VALIDATION_ERROR,
                $errors
            );

            return $jsonErrorFactory->createResponse($jsonError);
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
                                    'workout_report', 
                                    [
                                        'id' => $workout->getId()
                                    ], 
                                    UrlGeneratorInterface::ABSOLUTE_URL
                                )
                    ];

                    $this->addFlash('success', 'Workout was created!');

                    return new JsonResponse($response, Response::HTTP_OK);
                } catch (\Exception $e) {
                    $jsonError = new JsonErrorResponse(400, 
                        JsonErrorResponse::TYPE_ACTION_FAILED,
                        $e->getMessage()
                    );

                    return $jsonErrorFactory->createResponse($jsonError);
                }

            } else {
                $jsonError = new JsonErrorResponse(400, 
                    JsonErrorResponse::TYPE_MODEL_VALIDATION_ERROR,
                    $modelValidator->getErrorMessage()
                );

                return $jsonErrorFactory->createResponse($jsonError);
            }
        }        
        
        $jsonError = new JsonErrorResponse(400, 
            JsonErrorResponse::TYPE_ACTION_FAILED,
            'Cannot add workout. Please check data and try again.'
        );

        return $jsonErrorFactory->createResponse($jsonError);
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

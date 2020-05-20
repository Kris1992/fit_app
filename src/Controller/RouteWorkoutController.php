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
use App\Exception\Api\ApiBadRequestHttpException;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;
use App\Services\JsonErrorResponse\JsonErrorResponseTypes;

use App\Services\FormApiValidator\FormApiValidatorInterface;

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
    public function addDrawedAction(Request $request, WorkoutSpecificExtender $workoutSpecificExtender, EntityManagerInterface $entityManager, ModelValidatorInterface $modelValidator, JsonErrorResponseFactory $jsonErrorFactory, FormApiValidatorInterface $formApiValidator)
    {
        $data = json_decode($request->getContent(), true);
        if($data === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $form = $this->createForm(WorkoutWithMapFormType::class);
        $form->submit($data['formData']);

        if (!$form->isValid()) {
            return $jsonErrorFactory->createResponse(
                400, 
                JsonErrorResponseTypes::TYPE_FORM_VALIDATION_ERROR,
                $formApiValidator->getErrors($form)
            );
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

                    $entityManager->persist($workout);
                    $entityManager->flush();
                    
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
                    return $jsonErrorFactory->createResponse(400, JsonErrorResponseTypes::TYPE_ACTION_FAILED, null, $e->getMessage());
                }

            } else {
                return $jsonErrorFactory->createResponse(400, JsonErrorResponseTypes::TYPE_ACTION_FAILED, null, $modelValidator->getErrorMessage());
            }
        }        
        
        return $jsonErrorFactory->createResponse(400, JsonErrorResponseTypes::TYPE_ACTION_FAILED, null, 'Cannot add workout. Please check data and try again.');
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
    public function addTrackedAction(Request $request, WorkoutSpecificExtender $workoutSpecificExtender, EntityManagerInterface $entityManager, ModelValidatorInterface $modelValidator, JsonErrorResponseFactory $jsonErrorFactory, FormApiValidatorInterface $formApiValidator)
    {
        $data = json_decode($request->getContent(), true);

        if($data === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $form = $this->createForm(WorkoutWithMapFormType::class, null, [ 'is_drawing' => false ]);

        $form->submit($data['formData']);

        if (!$form->isValid()) {
            return $jsonErrorFactory->createResponse(
                400, 
                JsonErrorResponseTypes::TYPE_FORM_VALIDATION_ERROR, 
                $formApiValidator->getErrors($form)
            );
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

                    $entityManager->persist($workout);
                    $entityManager->flush();
                    
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
                    return $jsonErrorFactory->createResponse(400, JsonErrorResponseTypes::TYPE_ACTION_FAILED, null, $e->getMessage());
                }

            } else {
                return $jsonErrorFactory->createResponse(400, JsonErrorResponseTypes::TYPE_MODEL_VALIDATION_ERROR, null, $modelValidator->getErrorMessage());
            }
        }        
        
        return $jsonErrorFactory->createResponse(400, JsonErrorResponseTypes::TYPE_ACTION_FAILED, null, 'Cannot add workout. Please check data and try again.');
    }
}

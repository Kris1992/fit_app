<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\WorkoutRepository;
use App\Repository\AbstractActivityRepository;
use App\Entity\Workout;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\WorkoutSpecificDataFormType;
use App\Form\WorkoutAverageDataFormType;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Form\Model\Workout\WorkoutAverageFormModel;
use App\Services\ModelExtender\WorkoutSpecificExtender;
use App\Services\ModelExtender\WorkoutAverageExtender;
use App\Exception\Api\ApiBadRequestHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Services\ModelValidator\ModelValidatorInterface;
use App\Services\ModelValidator\ModelValidatorChooser;
use App\Services\Factory\Workout\WorkoutFactory;
use App\Services\Updater\Workout\WorkoutUpdaterInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\Command\DeleteWorkoutImage;
use App\Services\ImagesManager\ImagesManagerInterface;
use App\Services\JsonErrorResponse\JsonErrorResponse;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;

use Symfony\Component\Form\FormInterface;

class WorkoutController extends AbstractController
{
    /**
     * @Route("/workout/list", name="workout_list")
     * @IsGranted("ROLE_USER")
     */
    public function list(WorkoutRepository $workoutRepository, Request $request, EntityManagerInterface $em, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator, AbstractActivityRepository $activityRepository)
    {
        //TO DO
    	$user = $this->getUser();
    	$workouts = $workoutRepository->findBy([ 'user' => $user ]);
        $movementActivitiesNames = $activityRepository->findByTypeUniqueNamesAlphabetical('Movement');

        $formAverage = $this->createForm(WorkoutAverageDataFormType::class);
        $formSpecific = $this->createForm(WorkoutSpecificDataFormType::class);
        $formAverage->handleRequest($request);

        if ($formAverage->isSubmitted() && $formAverage->isValid()) {
            $workoutAverageFormModel = $formAverage->getData();
            $workoutAverageFormModel = $workoutAverageExtender->fillWorkoutModel($workoutAverageFormModel, $this->getUser());
            
            //Validation Model data
            $isValid = $modelValidator->isValid($workoutAverageFormModel, ['model']);
                    
            if ($isValid) {
                $activity = $workoutAverageFormModel->getActivity();
                $workoutFactory = WorkoutFactory::chooseFactory($activity->getType());
                $workout = $workoutFactory->createWorkout($workoutAverageFormModel);

                $em->persist($workout);
                $em->flush();

                $this->addFlash('success', 'Workout was added!! ');
                return $this->redirectToRoute('workout_list');
            } else {
                $errors = $modelValidator->getErrors();
                return $this->render('workout/add.html.twig', [
                    'workoutForm' => $formAverage->createView(),
                    'workoutSpecificDataForm' => $formSpecific->createView(),
                    'errors' => $errors,
                ]);
            }
        }

        return $this->render('workout/list.html.twig', [
            'workouts' => $workouts,
            'workoutForm' => $formAverage->createView(),
            'formSpecific' => $formSpecific->createView(),
            'movementActivities' => $movementActivitiesNames
        ]);

    }
    
    /**
     * @Route("/workout/stats", name="workout_stats")
     * @IsGranted("ROLE_USER")
     */
    public function stats(Request $request)
    {
        return $this->render('workout/stats.html.twig');
    }

    /**
     * @Route("api/workout/get_id_by_date", name="api_workout_id_by_date")
     * @IsGranted("ROLE_USER")
     */
    public function getWorkoutIdByDateAction(WorkoutRepository $workoutRepository, Request $request, EntityManagerInterface $em)
    {
        $timeline = json_decode($request->getContent(), true);

        if($timeline === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $timeline['stopDate'] .= ' 23:59:59';
        $user = $this->getUser();
        $workouts = $workoutRepository->findByUserAndDateArray($user, $timeline);
       // $workouts = $workoutRepository->findByUserAndDateArrayNative($user, $timeline);

        //$workouts = $workoutRepository->findByUserAndDate($user, $timeline); if we want whole workout object
        
        return $this->json(
            $workouts,
            200,
            [],
            []
        );

    }

    /**
     * @Route("api/workout/get_energy_by_date", name="api_workout_energy_by_date")
     * @IsGranted("ROLE_USER")
     */
    public function getWorkoutEnergyByDateAction(WorkoutRepository $workoutRepository, Request $request, EntityManagerInterface $em)
    {
        $days = json_decode($request->getContent(), true);

        if($days === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $timeline['startDate'] = $days[0];
        $timeline['stopDate'] = end($days);
        $timeline['stopDate'] .= ' 23:59:59';//To get all workouts during the last day

        /**
         * @var User $user
         */
        $user = $this->getUser();
        $workouts = $workoutRepository->countEnergyPerDayByUserAndDateArray($user, $timeline);

        $energyPerDay = array();
        $index = 0;

        if(!empty($workouts)) {
            foreach ($days as $day) {
                if (array_key_exists($index, $workouts) && $day == $workouts[$index]['startDate']) {
                    $currentEnergy = $workouts[$index]['burnoutEnergy'];
                    $index++;
                } else {
                    $currentEnergy = 0;
                }

                array_push($energyPerDay, $currentEnergy);
            }
        } else {
            foreach ($days as $day) {
                $currentEnergy = 0;
                array_push($energyPerDay, $currentEnergy);
            }
        }

        return $this->json(
            $energyPerDay,
            200,
            [],
            []
        );
    }

    /**
     * @Route("/workout/choose_add", name="workout_choose_add", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function addPanel(Request $request)
    {
    
        return $this->render('workout/addPanel.html.twig', []);
    }

    /**
     * @Route("/workout/add_average", name="workout_add_average", methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function addAverage(Request $request, EntityManagerInterface $em, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator, ModelValidatorChooser $validatorChooser)
    {
        $formAverage = $this->createForm(WorkoutAverageDataFormType::class);
        $formAverage->handleRequest($request);

        if ($formAverage->isSubmitted() && $formAverage->isValid()) {
            $workoutAverageFormModel = $formAverage->getData();
            $workoutAverageFormModel = $workoutAverageExtender->fillWorkoutModel($workoutAverageFormModel, $this->getUser(), $formAverage['imageFile']->getData());  
            
            if($workoutAverageFormModel) {
                //Validation Model data
                $validationGroup = $validatorChooser->chooseValidationGroup($workoutAverageFormModel->getType());
                $isValid = $modelValidator->isValid($workoutAverageFormModel, $validationGroup);
                    
                if ($isValid) {
                    try {
                        $activity = $workoutAverageFormModel->getActivity();
                        $workoutFactory = WorkoutFactory::chooseFactory($activity->getType());
                        $workout = $workoutFactory->create($workoutAverageFormModel);

                        $em->persist($workout);
                        $em->flush();

                        $this->addFlash('success', 'Workout was added!');
                        return $this->redirectToRoute('workout_list');
                    } catch (\Exception $e) {
                        $this->addFlash('warning', $e->getMessage());
                        return $this->render('workout/add_average.html.twig', [
                            'workoutForm' => $formAverage->createView(),
                        ]);
                    }

                } else {
                    $errors = $modelValidator->getErrors();
                    return $this->render('workout/add_average.html.twig', [
                        'workoutForm' => $formAverage->createView(),
                        'errors' => $errors,
                    ]);
                }
            }
            $this->addFlash('warning', 'Cannot create workout with that type of activity.');
        }

        return $this->render('workout/add_average.html.twig', [
            'workoutForm' => $formAverage->createView(),
        ]);
    }

    /**
     * @Route("/workout/add_specific", name="workout_add_specific", methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function addSpecific(Request $request, EntityManagerInterface $em, WorkoutSpecificExtender $workoutSpecificExtender, ModelValidatorInterface $modelValidator, ModelValidatorChooser $validatorChooser)
    {
        $formSpecific = $this->createForm(WorkoutSpecificDataFormType::class);
            
        $formSpecific->handleRequest($request);
        if ($formSpecific->isSubmitted() && $formSpecific->isValid()) {
            $workoutSpecificModel = $formSpecific->getData();
            $workoutSpecificModel = $workoutSpecificExtender->fillWorkoutModel($workoutSpecificModel, $this->getUser(), $formSpecific['imageFile']->getData());

            if ($workoutSpecificModel) {
                //Validation Model data
                $validationGroup = $validatorChooser->chooseValidationGroup($workoutSpecificModel->getType());
                $isValid = $modelValidator->isValid($workoutSpecificModel, $validationGroup);

                if ($isValid) {
                    try {
                        $workoutFactory = WorkoutFactory::chooseFactory($workoutSpecificModel->getType());
                        $workout = $workoutFactory->create($workoutSpecificModel);

                        $em->persist($workout);
                        $em->flush();

                        $this->addFlash('success', 'Workout was added!');
                        return $this->redirectToRoute('workout_list');
                    } catch (\Exception $e) {
                        $this->addFlash('warning', $e->getMessage());
                        return $this->render('workout/add_specific.html.twig', [
                            'workoutSpecificDataForm' => $formSpecific->createView(),
                        ]);
                    }

                } else {
                    $errors = $modelValidator->getErrors();
                    return $this->render('workout/add_specific.html.twig', [
                        'workoutSpecificDataForm' => $formSpecific->createView(),
                        'errors' => $errors,
                    ]);
                }
            }

        $this->addFlash('warning', 'Sorry we dont have activity matching your achievements in database.');
        }

        return $this->render('workout/add_specific.html.twig', [
            'workoutSpecificDataForm' => $formSpecific->createView(),
        ]);
    }

    /**
     * @Route("/workout/{id}/delete", name="workout_delete",  methods={"DELETE"})
     */
    public function delete(Request $req, Workout $workout, EntityManagerInterface $em, MessageBusInterface $messageBus)
    {
        $this->denyAccessUnlessGranted('MANAGE', $workout);

        if ($workout->getImageFilename()) {
            //clear users files (all images and folders)
            $subdirectory = $workout->getUser()->getLogin();
            $messageBus->dispatch(new DeleteWorkoutImage($subdirectory, $workout->getImageFilename()));
        }

        $em->remove($workout);
        $em->flush();
    
        return new Response(null, 204);
    }

    /**
     * @Route("/api/workout/add_average", name="api_workout_add_average", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function addAverageAction(Request $request, EntityManagerInterface $em, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator, ModelValidatorChooser $validatorChooser, JsonErrorResponseFactory $jsonErrorFactory)
    {

       $formAverage = $this->createForm(WorkoutAverageDataFormType::class);
        if ($request->getContent() !== '') {
            $data = json_decode($request->getContent(), true);
            if($data === null) {
                throw new ApiBadRequestHttpException('Invalid JSON.');    
            }
            //TO DO upload file
             
            $formAverage->submit($data);
        } else {
            $formAverage->handleRequest($request);
        }
        
        if ($formAverage->isSubmitted() && $formAverage->isValid()) {
            $workoutAverageFormModel = $formAverage->getData();
            $workoutAverageFormModel = $workoutAverageExtender->fillWorkoutModel($workoutAverageFormModel, $this->getUser(), $formAverage['imageFile']->getData());

            if($workoutAverageFormModel) {
                //Validation Model data
                $validationGroup = $validatorChooser->chooseValidationGroup($workoutAverageFormModel->getType());
                $isValid = $modelValidator->isValid($workoutAverageFormModel, $validationGroup);
                if ($isValid) {
                    try {
                        $activity = $workoutAverageFormModel->getActivity();
                        $workoutFactory = WorkoutFactory::chooseFactory($activity->getType());
                        $workout = $workoutFactory->create($workoutAverageFormModel);
                        $em->persist($workout);
                        $em->flush();
                    
                        $response = new Response(null, 201);
                        $response->headers->set(
                            'Location',
                            $this->generateUrl('api_workout_get', ['id' => $workout->getId()])
                        );
        
                        return $response;
                    } catch (\Exception $e) {
                        $jsonError = new JsonErrorResponse(400, 
                            JsonErrorResponse::TYPE_ACTION_FAILED,
                            $e->getMessage()
                        ); 
                    }
                } else {
                    $jsonError = new JsonErrorResponse(400, 
                        JsonErrorResponse::TYPE_MODEL_VALIDATION_ERROR,
                        $modelValidator->getErrorMessage()
                    );
                }
            } else {
                $jsonError = new JsonErrorResponse(400, 
                    JsonErrorResponse::TYPE_ACTION_FAILED,
                    'Cannot create workout with that type of activity.'
                );
            }
        } else {
            $jsonError = new JsonErrorResponse(400, 
                JsonErrorResponse::TYPE_FORM_VALIDATION_ERROR,
                null
            );
            $jsonError->setArrayExtraData($this->getErrorsFromForm($formAverage));
        }

        return $jsonErrorFactory->createResponse($jsonError);
    }

    /**
     * @Route("/api/workout/add_specific", name="api_workout_add_specific", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function addSpecificAction(Request $request, EntityManagerInterface $em, WorkoutSpecificExtender $workoutSpecificExtender, ModelValidatorInterface $modelValidator, ModelValidatorChooser $validatorChooser, JsonErrorResponseFactory $jsonErrorFactory)
    {

        $data = json_decode($request->getContent(), true);
        
        if($data === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $formSpecific = $this->createForm(WorkoutSpecificDataFormType::class);
        $formSpecific->submit($data);

        if ($formSpecific->isSubmitted() && $formSpecific->isValid()) {
            $workoutSpecificModel = $formSpecific->getData();
            $workoutSpecificModel = $workoutSpecificExtender->fillWorkoutModel($workoutSpecificModel, $this->getUser(), $formSpecific['imageFile']->getData());
            
            if($workoutSpecificModel) {
                //Validation Model data
                $validationGroup = $validatorChooser->chooseValidationGroup($workoutSpecificModel->getType());
                $isValid = $modelValidator->isValid($workoutSpecificModel, $validationGroup);

                if ($isValid) {
                    try {
                        $activity = $workoutSpecificModel->getActivity();
                        $workoutFactory = WorkoutFactory::chooseFactory($activity->getType());
                        $workout = $workoutFactory->create($workoutSpecificModel);
                        $em->persist($workout);
                        $em->flush();
                    
                        $response = new Response(null, 201);
                        $response->headers->set(
                            'Location',
                            $this->generateUrl('api_workout_get', ['id' => $workout->getId()])
                        );
        
                        return $response;
                    } catch (\Exception $e) {
                        $jsonError = new JsonErrorResponse(400, 
                            JsonErrorResponse::TYPE_ACTION_FAILED,
                            $e->getMessage()
                        ); 
                    }
                } else {
                    $jsonError = new JsonErrorResponse(400, 
                        JsonErrorResponse::TYPE_MODEL_VALIDATION_ERROR,
                        $modelValidator->getErrorMessage()
                    );
                }
            } else {
                $jsonError = new JsonErrorResponse(400, 
                    JsonErrorResponse::TYPE_ACTION_FAILED,
                    'Sorry we dont have activity matching your achievements in database.'
                );
            }
        } else {
            $jsonError = new JsonErrorResponse(400, 
                JsonErrorResponse::TYPE_FORM_VALIDATION_ERROR,
                null
            );
            $jsonError->setArrayExtraData($this->getErrorsFromForm($formSpecific));
        }

        return $jsonErrorFactory->createResponse($jsonError);

    }

     /**
     * @Route("/api/workout_get/{id}", name="api_workout_get", methods={"GET"})
     */
    public function getWorkoutAction(Workout $workout)
    {
        $workout->transformSaveTimeToString();

        $startAt = $workout->getStartAt();
        $startAt = date_format($startAt, 'Y-m-d H:i');
        $workout->setStartDate($startAt);

        $linkDelete = $this->generateUrl('workout_delete', ['id' => $workout->getId()]);
        $linkEdit = $this->generateUrl('api_workout_edit', ['id' => $workout->getId()]);

        $workout->setLinks('delete',$linkDelete);
        $workout->setLinks('edit',$linkEdit);

        return $this->json(
            $workout,
            201,
            [],
            [
                'groups' => ['main']
            ]
        );
    }

    /**
     * @Route("/api/workout/{id}/edit", name="api_workout_edit", methods={"PUT"})
     */
    public function editAction(Workout $workout, Request $request, EntityManagerInterface $em, WorkoutSpecificExtender $workoutSpecificExtender, ModelValidatorInterface $modelValidator, ModelValidatorChooser $validatorChooser, WorkoutUpdaterInterface $workoutUpdater, JsonErrorResponseFactory $jsonErrorFactory)
    {

        $this->denyAccessUnlessGranted('MANAGE', $workout);

        $data = json_decode($request->getContent(), true);
        //dump(date_default_timezone_get());

        if($data === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $formSpecific = $this->createForm(WorkoutSpecificDataFormType::class);
        $formSpecific->submit($data);

        if (!$formSpecific->isValid()) {
            $jsonError = new JsonErrorResponse(400, 
                JsonErrorResponse::TYPE_FORM_VALIDATION_ERROR,
                null
            );
            $jsonError->setArrayExtraData($this->getErrorsFromForm($formSpecific));

            return $jsonErrorFactory->createResponse($jsonError);
        }

        $workoutSpecificModel = $formSpecific->getData();
        $workoutSpecificModel = $workoutSpecificExtender->fillWorkoutModel($workoutSpecificModel, $this->getUser(), null);

        if ($workoutSpecificModel) {
            //Validation Model data
            $validationGroup = $validatorChooser->chooseValidationGroup($workoutSpecificModel->getType());
            $isValid = $modelValidator->isValid($workoutSpecificModel, $validationGroup);

            if ($isValid) {
                try {
                    $workout = $workoutUpdater->update($workoutSpecificModel, $workout);
                    $em->persist($workout);
                    $em->flush();
                    $response = new Response(null, 200);

                    $response->headers->set(
                        'Location',
                        $this->generateUrl('api_workout_get', ['id' => $workout->getId()])
                    );

                    return $response;
                } catch (\Exception $e) {
                    $jsonError = new JsonErrorResponse(400, 
                        JsonErrorResponse::TYPE_ACTION_FAILED,
                        $e->getMessage()
                    );
                }
            } else {
                $jsonError = new JsonErrorResponse(400, 
                    JsonErrorResponse::TYPE_MODEL_VALIDATION_ERROR,
                    $modelValidator->getErrorMessage()
                );
            }
        } else {
            $jsonError = new JsonErrorResponse(400, 
                JsonErrorResponse::TYPE_ACTION_FAILED,
                'Cannot update workout with that type of activity.'
            );
        }

        return $jsonErrorFactory->createResponse($jsonError);
    }

     /**
     * @Route("/api/specific_workout_form", name="api_workout_specific_form")
     */
    public function getSpecificWorkoutFormAction(Request $request, AbstractActivityRepository $activityRepository)
    {
        $name = $request->query->get('activityName');

        if($name === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $activity = $activityRepository->findOneBy(['name' => $name]);
        $type = $activity->getType();

        $workout = new WorkoutSpecificFormModel();
        $workout->setType($type);
        $formSpecific = $this->createForm(WorkoutSpecificDataFormType::class, $workout);
        
        return $this->render('forms/workout_specific_additional_data_form.html.twig', [
            'workoutSpecificDataForm' => $formSpecific->createView(),
        ]);
    }

    /**
     * @Route("/api/server_date", name="server_date_get", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function getServerDateAction()
    {
        $today = new \DateTime();

        return $this->json(
            $today,
            201,
            [],
            []
        );
    }

    /**
     * @Route("/api/workout/get_workout_sets_form", name="api_workout_sets_form")
     */
    public function getWorkoutSetsForm(Request $request, AbstractActivityRepository $activityRepository)
    {
        $activityId = $request->query->get('id');
        
        if($activityId === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $activity = $activityRepository->find($activityId);
        $type = $activity->getType();
        
        $workoutAverageFormModel = new WorkoutAverageFormModel();
        $workoutAverageFormModel->setType($type);
        
        $formAverage = $this->createForm(WorkoutAverageDataFormType::class, $workoutAverageFormModel);
        
        return $this->render('forms/workout_sets_form.html.twig', [
            'workoutForm' => $formAverage->createView(),
        ]);
    }

    /**
     * @Route("/api/workout/workouts_get_after_date", name="api_workouts_get_after_date", 
     * methods={"POST"})
     */
    public function getWorkoutsAfterDateAction(Request $request, WorkoutRepository $workoutRepository, ImagesManagerInterface $workoutsImagesManager, JsonErrorResponseFactory $jsonErrorFactory)
    {
        /** @var User $user */
        $user = $this->getUser();
        $date = json_decode($request->getContent(), true);

        if(!strtotime($date)){
            $jsonError = new JsonErrorResponse(404, 
                JsonErrorResponse::TYPE_NOT_FOUND_ERROR,
                'No workouts to load.'
            );

            return $jsonErrorFactory->createResponse($jsonError);
        }

        $workouts = $workoutRepository->findByUserBeforeDate($user, $date, 10);

        if (!$workouts) {
            $jsonError = new JsonErrorResponse(404, 
                JsonErrorResponse::TYPE_NOT_FOUND_ERROR,
                'No more workouts to load.'
            );

            return $jsonErrorFactory->createResponse($jsonError);
        }

        foreach ($workouts as $workout) {
            $workout->transformSaveTimeToString();
            $startAt = $workout->getStartAt();
            $startAt = date_format($startAt, 'Y-m-d H:i');
            $workout->setStartDate($startAt);
            $workout->setLinks(
                'thumbImage', 
                $workoutsImagesManager->getPublicPath($workout->getThumbImagePath())
            );
            $workout->setLinks(
                'image', 
                $workoutsImagesManager->getPublicPath($workout->getImagePath())
            );
            $workout->setLinks(
                'reaction', 
                $this->generateUrl('api_workout_reaction', ['id' => $workout->getId()])
            );
            $workout->setReactionsArray($user, [1,2]);
        }

        return $this->json(
            $workouts,
            200,
            ['content-type' => 'application/hal+json'],
            [
                'groups' => ['main']
            ]
        );
    }

     /**
     * @Route("/workout/{id}/report", name="workout_report", methods={"GET"})
     */
    public function workoutReport(Request $request, Workout $workout)
    {
        $this->denyAccessUnlessGranted('MANAGE', $workout);


        return $this->render('workout/report.html.twig', [
            'workout' => $workout 
        ]);
    }
    
    //bind it to service
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

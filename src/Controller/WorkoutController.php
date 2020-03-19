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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Services\ModelValidator\ModelValidatorInterface;
use App\Services\Factory\Workout\WorkoutFactory;
use App\Services\Updater\Workout\WorkoutUpdaterInterface;

class WorkoutController extends AbstractController
{
    /**
     * @Route("/workout/list", name="workout_list")
     * @IsGranted("ROLE_USER")
     */
    public function list(WorkoutRepository $workoutRepository, Request $request, EntityManagerInterface $em, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator)
    {

    	$user = $this->getUser();
    	$workouts = $workoutRepository->findBy(['user' => $user ]);

        $formAverage = $this->createForm(WorkoutAverageDataFormType::class);
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
            'workoutForm' => $formAverage->createView()
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
     * @Route("api/workout/get_id_by_date", name="workout_id_by_date")
     * @IsGranted("ROLE_USER")
     */
    public function getWorkoutIdByDate(WorkoutRepository $workoutRepository, Request $request, EntityManagerInterface $em)
    {
        $timeline = json_decode($request->getContent(), true);

        if($timeline === null) {
            throw new BadRequestHttpException('Invalid Json');    
        }

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
     * @Route("api/workout/get_energy_by_date", name="workout_energy_by_date")
     * @IsGranted("ROLE_USER")
     */
    public function getWorkoutEnergyByDate(WorkoutRepository $workoutRepository, Request $request, EntityManagerInterface $em)
    {
        $days = json_decode($request->getContent(), true);

        if($days === null) {
            throw new BadRequestHttpException('Invalid Json');    
        }

        $timeline['startDate'] = $days[0];
        $timeline['stopDate'] = end($days);

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
    
        return $this->render('workout/addPanel.html.twig', [
            
        ]);
    }

    /**
     * @Route("/workout/add_average", name="workout_add_average", methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function addAverage(Request $request, EntityManagerInterface $em, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator)
    {
        $formAverage = $this->createForm(WorkoutAverageDataFormType::class);

        $formAverage->handleRequest($request);           
        if ($formAverage->isSubmitted() && $formAverage->isValid()) {
            $workoutAverageFormModel = $formAverage->getData();
            $workoutAverageFormModel = $workoutAverageExtender->fillWorkoutModel($workoutAverageFormModel, $this->getUser());  
            
            //Validation Model data
            $isValid = $modelValidator->isValid($workoutAverageFormModel, ['model']);
                    
            if ($isValid) {
                $activity = $workoutAverageFormModel->getActivity();
                $workoutFactory = WorkoutFactory::chooseFactory($activity->getType());
                $workout = $workoutFactory->create($workoutAverageFormModel);

                $em->persist($workout);
                $em->flush();

                $this->addFlash('success', 'Workout was added!! ');
                return $this->redirectToRoute('workout_list');
            } else {
                $errors = $modelValidator->getErrors();
                return $this->render('workout/add_average.html.twig', [
                    'workoutForm' => $formAverage->createView(),
                    'errors' => $errors,
                ]);
            }
        }

        return $this->render('workout/add_average.html.twig', [
            'workoutForm' => $formAverage->createView(),
        ]);
    }

    /**
     * @Route("/workout/add_specific", name="workout_add_specific", methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function addSpecific(Request $request, EntityManagerInterface $em, WorkoutSpecificExtender $workoutSpecificExtender, ModelValidatorInterface $modelValidator)
    {
        $formSpecific = $this->createForm(WorkoutSpecificDataFormType::class);
            
        $formSpecific->handleRequest($request);
        if ($formSpecific->isSubmitted() && $formSpecific->isValid()) {
            $workoutSpecificModel = $formSpecific->getData();
            
            $workoutSpecificModel = $workoutSpecificExtender->fillWorkoutModel($workoutSpecificModel, $this->getUser());

            if (!$workoutSpecificModel) {
                $this->addFlash('warning', 'Sorry we dont had activity matching your achievements in database');
                return $this->redirectToRoute('workout_add_specific');
            }

            //Validation Model data
            $isValid = $modelValidator->isValid($workoutSpecificModel, ['model']);
            if ($isValid) {
                $workoutFactory = WorkoutFactory::chooseFactory($workoutSpecificModel->getType());
                $workout = $workoutFactory->create($workoutSpecificModel);

                $em->persist($workout);
                $em->flush();

                $this->addFlash('success', 'Workout was added!! ');
                return $this->redirectToRoute('workout_list');
            } else {
                $errors = $modelValidator->getErrors();
                return $this->render('workout/add_specific.html.twig', [
                    'workoutSpecificDataForm' => $formSpecific->createView(),
                    'errors' => $errors,
                ]);
            }
        }

        return $this->render('workout/add_specific.html.twig', [
            'workoutSpecificDataForm' => $formSpecific->createView(),
        ]);
    }

     /**
     * @Route("/workout/add_drawed", name="workout_add_drawed", methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function addDrawed(Request $request, EntityManagerInterface $em, WorkoutSpecificExtender $workoutSpecificExtender, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator,string $map_api_key)
    {


        return $this->render('workout/add_drawed.html.twig', [
            'map_api_key' => $map_api_key
            //'workoutForm' => $formAverage->createView(),
            //'workoutSpecificDataForm' => $formSpecific->createView(),
        ]);
    }

    /**
     * @Route("/workout/delete/{id}", name="workout_delete",  methods={"DELETE"})
     */
    public function delete(Request $req, Workout $workout, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('MANAGE', $workout);

        $em->remove($workout);
        $em->flush();
    
        return new Response(null, 204);
    }

    /**
     * @Route("/api/workout/add", name="workout_add", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request, EntityManagerInterface $em, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator)
    {
        $data = json_decode($request->getContent(), true);

        if($data === null) {
            throw new BadRequestHttpException('Invalid Json');    
        }

        $formAverage = $this->createForm(WorkoutAverageDataFormType::class);
        $formAverage->submit($data);

        if (!$formAverage->isValid()) {
            $errors = $this->getErrorsFromForm($formAverage);

            return $this->json(
            $errors,
            400
            );
        }
        
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
            $response = new Response(null, 201);


            $response->headers->set(
                'Location',
                $this->generateUrl('workout_get', ['id' => $workout->getId()])
            );
        
            return $response;
        } else {
            $validatorErrors = $modelValidator->getErrors();
                        
            $errors = [
                'activity' => $validatorErrors[0]->getMessage()
                //'Calculate data goes wrong. Probably your workout duration is too short'
            ];
        
            return $this->json(
                $errors,
                400
            );
        }
    }

     /**
     * @Route("/api/workout_get/{id}", name="workout_get", methods={"GET"})
     */
    public function getWorkoutAction(Workout $workout)
    {
        $workout->transformSaveTimeToString();

        $startAt = $workout->getStartAt();
        $startAt = date_format($startAt, 'Y-m-d H:i');
        $workout->setStartDate($startAt);

        $linkDelete = $this->generateUrl('workout_delete', ['id' => $workout->getId()]);
        $linkEdit = $this->generateUrl('workout_edit', ['id' => $workout->getId()]);

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
     * @Route("/api/workout/edit/{id}", name="workout_edit", methods={"PUT"})
     */
    public function edit(Workout $workout, Request $request, EntityManagerInterface $em, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator, WorkoutUpdaterInterface $workoutUpdater)
    {
        $this->denyAccessUnlessGranted('MANAGE', $workout);

        $data = json_decode($request->getContent(), true);
        //dump(date_default_timezone_get());

        if($data === null) {
            throw new BadRequestHttpException('Invalid Json');    
        }

        $formAverage = $this->createForm(WorkoutAverageDataFormType::class);
        $formAverage->submit($data);

        if (!$formAverage->isValid()) {
            $errors = $this->getErrorsFromForm($formAverage);

            return $this->json(
            $errors,
            400
            );
        }

        $workoutAverageFormModel = $formAverage->getData();
        $workoutAverageFormModel = $workoutAverageExtender->fillWorkoutModel($workoutAverageFormModel, $workout->getUser());
                
        //Validation Model data
        $isValid = $modelValidator->isValid($workoutAverageFormModel, ['model']);
        if ($isValid) {
            $workout = $workoutUpdater->update($workoutAverageFormModel, $workout);
            $em->persist($workout);
            $em->flush();

            $response = new Response(null, 201);

            $response->headers->set(
                'Location',
                $this->generateUrl('workout_get', ['id' => $workout->getId()])
            );

            return $response;
        }

        //We can't display unmapped errors in list so just empty response
        return $this->json(null, 400);
    }

     /**
     * @Route("/api/specific_workout_form", name="api_workout_specific_form")
     */
    public function getSpecificWorkoutFormAction(Request $request, AbstractActivityRepository $activityRepository)
    {
        $name = $request->query->get('activityName');

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
    public function getWorkoutsAfterDateAction(Request $request, WorkoutRepository $workoutRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $date = json_decode($request->getContent(), true);
        if(!strtotime($date)){
            return new JsonResponse(['message' => 'Wrong date format. Cannot load more data.'], Response::HTTP_BAD_REQUEST);
        }

        $workouts = $workoutRepository->findByUserBeforeDate($user, $date, 10);

        if (!$workouts) {
            return new JsonResponse(['message' => 'No more workouts to load.'], Response::HTTP_BAD_REQUEST);
        }
        foreach ($workouts as $workout) {
            $workout->transformSaveTimeToString();
            $startAt = $workout->getStartAt();
            $startAt = date_format($startAt, 'Y-m-d H:i');
            $workout->setStartDate($startAt);
        }

        return $this->json(
            $workouts,
            200,
            [],
            [
                'groups' => ['main']
            ]
        );
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

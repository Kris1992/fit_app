<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\WorkoutRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Workout;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\WorkoutSpecificDataFormType;
use App\Form\WorkoutAverageDataFormType;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Form\Model\Workout\WorkoutAverageFormModel;
use App\Services\ModelExtender\WorkoutSpecificExtender;
use App\Services\ModelExtender\WorkoutAverageExtender;
use App\Services\ModelValidator\ModelValidatorInterface;
use App\Services\ModelValidator\ModelValidatorChooser;
use App\Services\Factory\Workout\WorkoutFactory;
use App\Services\Updater\Workout\WorkoutUpdaterInterface;
use App\Services\Factory\WorkoutModel\WorkoutModelFactory;
use App\Services\ImagesManager\ImagesManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
* @IsGranted("ROLE_ADMIN")
**/
class AdminWorkoutController extends AbstractController
{
    
 	/**
     * @Route("/admin/workout", name="admin_workout_list", methods={"GET"})
     */
    public function list(WorkoutRepository $workoutRepository, Request $request, PaginatorInterface $paginator)
    {
        $searchTerms = $request->query->getAlnum('filterValue');
        $workoutQuery = $workoutRepository->findAllQuery($searchTerms);

        $pagination = $paginator->paginate(
            $workoutQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('perPage', 5)/*limit per page*/
        );

        return $this->render('admin_workout/list.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/workout/add_average", name="admin_workout_add", methods={"POST", "GET"})
     */
    public function addAverage(Request $request, EntityManagerInterface $em, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator, ModelValidatorChooser $validatorChooser)
    {

        $formAverage = $this->createForm(WorkoutAverageDataFormType::class, null, [
            'is_admin' => true,
        ]);

        $formAverage->handleRequest($request);
        
        if ($formAverage->isSubmitted() && $formAverage->isValid()) {
            $workoutAverageFormModel = $formAverage->getData();
            $workoutAverageFormModel = $workoutAverageExtender->fillWorkoutModel($workoutAverageFormModel, null, $formAverage['imageFile']->getData());

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

                        $this->addFlash('success', 'Workout was created!!');
                        return $this->redirectToRoute('admin_workout_list');
                    } catch (\Exception $e) {
                        $this->addFlash('warning', $e->getMessage());
                        return $this->render('admin_workout/add_average.html.twig', [
                            'workoutForm' => $formAverage->createView(),
                        ]);
                    }

                } else {
                    $errors = $modelValidator->getErrors();
                    return $this->render('admin_workout/add_average.html.twig', [
                        'workoutForm' => $formAverage->createView(),
                        'errors' => $errors,
                    ]);
                } 
            }

            $this->addFlash('warning', 'Cannot create workout with this type of activity');
        }

        return $this->render('admin_workout/add_average.html.twig', [
            'workoutForm' => $formAverage->createView(),
        ]);
    }

    /**
     * @Route("/admin/workout/add_specific", name="admin_workout_add_specific", methods={"POST", "GET"})
     */
    public function addSpecific(Request $request, EntityManagerInterface $em, WorkoutSpecificExtender $workoutSpecificExtender, ModelValidatorInterface $modelValidator, ModelValidatorChooser $validatorChooser)
    {
        $formSpecific = $this->createForm(WorkoutSpecificDataFormType::class, null, [
            'is_admin' => true
        ]);
    
        $formSpecific->handleRequest($request);
        
        if ($formSpecific->isSubmitted() && $formSpecific->isValid()) {
            $workoutSpecificModel = $formSpecific->getData();
            $workoutSpecificModel = $workoutSpecificExtender->fillWorkoutModel($workoutSpecificModel, null, $formSpecific['imageFile']->getData());

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

                        $this->addFlash('success', 'Workout was created!! ');
                        return $this->redirectToRoute('admin_workout_list');
                    } catch (\Exception $e) {
                        $this->addFlash('warning', $e->getMessage());
                        return $this->render('admin_workout/add_specific.html.twig', [
                            'workoutSpecificDataForm' => $formSpecific->createView(),
                        ]);
                    }

                } else {
                    $errors = $modelValidator->getErrors();
                    return $this->render('admin_workout/add_specific.html.twig', [
                        'workoutSpecificDataForm' => $formSpecific->createView(),
                        'errors' => $errors,
                    ]);
                }
            }

            $this->addFlash('warning', 'Sorry we dont had activity matching your achievements in database');
        }

        return $this->render('admin_workout/add_specific.html.twig', [
            'workoutSpecificDataForm' => $formSpecific->createView(),
        ]);
    }

    /**
     * @Route("/admin/workout/edit_average/{id}", name="admin_workout_edit", methods={"POST", "GET"})
     */
    public function editAverage(Workout $workout, Request $request, EntityManagerInterface $em, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator, WorkoutUpdaterInterface $workoutUpdater, ModelValidatorChooser $validatorChooser)
    {
        $this->denyAccessUnlessGranted('MANAGE', $workout);

        try {
            $activity = $workout->getActivity();
            $workoutModelFactory = WorkoutModelFactory::chooseFactory($activity->getType(), 'Average');
            $workoutAverageFormModel = $workoutModelFactory->create($workout);
        
            $formAverage = $this->createForm(WorkoutAverageDataFormType::class, $workoutAverageFormModel, [
                    'is_admin' => true
                ]
            );
        } catch (\Exception $e) {
            $formAverage = $this->createForm(WorkoutAverageDataFormType::class, null, [
                'is_admin' => true
            ]);
            $this->addFlash('warning', $e->getMessage());
        }

        $formAverage->handleRequest($request);
        if ($formAverage->isSubmitted() && $formAverage->isValid()) {
            //$workoutModel = $formAverage->getData(); //form handles modeldata so we don't need it
            $workoutAverageFormModel = $workoutAverageExtender->fillWorkoutModel($workoutAverageFormModel, null, $formAverage['imageFile']->getData());

            if ($workoutAverageFormModel) {
                //Validation Model data
                $validationGroup = $validatorChooser->chooseValidationGroup($workoutAverageFormModel->getType());
                $isValid = $modelValidator->isValid($workoutAverageFormModel, $validationGroup);

                if ($isValid) {
                    try {
                        $workout = $workoutUpdater->update($workoutAverageFormModel, $workout);
                        $em->persist($workout);
                        $em->flush();

                        $this->addFlash('success', 'Workout is updated!');

                        return $this->redirectToRoute('admin_workout_edit', [
                            'id' => $workout->getId(),
                        ]);
                    } catch (\Exception $e) {
                        $this->addFlash('warning', $e->getMessage());
                    }
                } else {
                    $errors = $modelValidator->getErrors();
                
                    return $this->render('admin_workout/edit_average.html.twig', [
                        'workoutForm' => $formAverage->createView(),
                        'workoutId' => $workout->getId(),
                        'errors' => $errors,
                    ]);
                }
            } else {
                $this->addFlash('warning', 'Cannot update this type of activity');
            }
        }
       
        return $this->render('admin_workout/edit_average.html.twig', [
            'workoutForm' => $formAverage->createView(),
            'workoutId' => $workout->getId()
        ]);
    }

    /**
     * @Route("/admin/workout/edit_specific/{id}", name="admin_workout_specific_edit", 
     * methods={"POST", "GET"})
     */
    public function editSpecific(Workout $workout, Request $request, EntityManagerInterface $em, WorkoutSpecificExtender $workoutSpecificExtender, ModelValidatorInterface $modelValidator, WorkoutUpdaterInterface $workoutUpdater, ModelValidatorChooser $validatorChooser)
    {
        $this->denyAccessUnlessGranted('MANAGE', $workout);

        try {
            $activity = $workout->getActivity();
            $workoutModelFactory = WorkoutModelFactory::chooseFactory(
                $activity->getType(), 
                'Specific'
            );
            $workoutSpecificFormModel = $workoutModelFactory->create($workout);

            $formSpecific = $this->createForm(WorkoutSpecificDataFormType::class,
                $workoutSpecificFormModel, [
                'is_admin' => true
            ]);
        } catch (\Exception $e) {
            $formSpecific = $this->createForm(WorkoutSpecificDataFormType::class,
                null, [
                'is_admin' => true
            ]);
            $this->addFlash('warning', $e->getMessage());
        } //catch error TO DO
    
        $formSpecific->handleRequest($request);

        if ($formSpecific->isSubmitted() && $formSpecific->isValid()) {
            $workoutSpecificFormModel = $workoutSpecificExtender->fillWorkoutModel($workoutSpecificFormModel,null, $formSpecific['imageFile']->getData());

            if ($workoutSpecificFormModel) {
                //Validation Model data
                $validationGroup = $validatorChooser->chooseValidationGroup($workoutSpecificFormModel->getType());
                $isValid = $modelValidator->isValid($workoutSpecificFormModel, $validationGroup);

                if ($isValid) {
                    try {
                        $workout = $workoutUpdater->update($workoutSpecificFormModel, $workout);
                        $em->persist($workout);
                        $em->flush();

                        $this->addFlash('success', 'Workout is updated!');

                        return $this->redirectToRoute('admin_workout_specific_edit', [
                            'id' => $workout->getId(),
                        ]);
                    } catch (\Exception $e) {
                        $this->addFlash('warning', $e->getMessage());
                    }
                } else {
                    $errors = $modelValidator->getErrors();
                
                    return $this->render('admin_workout/edit_specific.html.twig', [
                        'workoutSpecificDataForm' => $formSpecific->createView(),
                        'workoutId' => $workout->getId(),
                        'errors' => $errors,
                    ]);
                }
            } else {
                $this->addFlash('warning', 'Cannot update this type of activity');
            }
        }
       
        return $this->render('admin_workout/edit_specific.html.twig', [
            'workoutSpecificDataForm' => $formSpecific->createView(),
            'workoutId' => $workout->getId()
        ]);
    }

    /**
     * @Route("/admin/workout/delete/{id}", name="admin_workout_delete",  methods={"DELETE"})
     */
    public function delete(Request $req, Workout $workout, EntityManagerInterface $em)
    {
        $em->remove($workout);
        $em->flush();
    
        $response = new Response();
        $this->addFlash('success','Workout was deleted!!');
        $response->send();
        return $response;
    }

    /**
     * @Route("/admin/workout/delete_selected", name="admin_workout_delete_selected",  methods={"POST", "DELETE"})
     */
    public function deleteSelected(Request $request,  EntityManagerInterface $entityManager, WorkoutRepository $workoutRepository)
    {
        //tutaj message bus i usuwanie zdjęć później
        $submittedToken = $request->request->get('token');
        if($request->request->has('deleteId')) {
            if ($this->isCsrfTokenValid('delete_multiple', $submittedToken)) {
                $ids = $request->request->get('deleteId');
                $workouts = $workoutRepository->findAllByIds($ids);
                if($workouts) {
                    foreach ($workouts as $workout) {
                        $entityManager->remove($workout);
                    }
                    $entityManager->flush();

                    $this->addFlash('success','Workouts were deleted!!');
                    return $this->redirectToRoute('admin_workout_list');
                }

                /* For now its not neccessary (admin can delete only 15 positions in one time)
                $batchSize = 10;
                $i = 1;
                foreach ($workouts as $workout) {
                    $entityManager->remove($workout);
                    if (($i % $batchSize) === 0) {
                        $entityManager->flush();
                        $entityManager->clear();
                    }
                ++$i;
                }
                $entityManager->flush();
                */
               
            } else {
                $this->addFlash('danger','Wrong token');
                return $this->redirectToRoute('admin_workout_list');
            }
        }

        $this->addFlash('warning','Nothing to do');
        return $this->redirectToRoute('admin_workout_list');
    }

     /**
     * @Route("/api/admin/workout/{id}/delete_image", name="api_admin_delete_workout_image",
     * methods={"DELETE"})
     */
    public function deleteWorkoutImageAction(Request $request, ImagesManagerInterface $workoutsImagesManager, EntityManagerInterface $entityManager, Workout $workout): Response
    {

        $data = json_decode($request->getContent(), true);
    
        if($data === null) {
            throw new BadRequestHttpException('Invalid Json');    
        }

        $workoutId = $workout->getId();
        $subdirectory = $workout->getUser()->getLogin();

        //double check that everything is ok
        if($workoutId == $data['id']) {
            $imageFilename = $workout->getImageFilename();
            if(!empty($imageFilename)) {
                $isDeleted = $workoutsImagesManager->deleteImage($imageFilename, $subdirectory);
                if ($isDeleted) {
                    $workout->setImageFilename(null);
                    $entityManager->persist($workout);
                    $entityManager->flush();
                }
                return new JsonResponse(Response::HTTP_OK);
            }
        }

        $responseMessage = [
            'errorMessage' => 'Image not found!'
        ];

        return new JsonResponse($responseMessage, Response::HTTP_BAD_REQUEST);
    }

}

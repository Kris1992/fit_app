<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
use App\Services\Factory\Workout\WorkoutFactory;
use App\Services\Updater\Workout\WorkoutUpdaterInterface;

use App\Services\Factory\WorkoutModel\WorkoutModelFactory;

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
    public function addAverage(Request $request, EntityManagerInterface $em, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator)
    {

        $formAverage = $this->createForm(WorkoutAverageDataFormType::class, null, [
            'is_admin' => true,
        ]);

        $formAverage->handleRequest($request);
        
        if ($formAverage->isSubmitted() && $formAverage->isValid()) {
            $workoutAverageFormModel = $formAverage->getData();
            $workoutAverageFormModel = $workoutAverageExtender->fillWorkoutModel($workoutAverageFormModel, null);

            //Validation Model data
            $isValid = $modelValidator->isValid($workoutAverageFormModel, ['model']);

            if ($isValid) {
                $activity = $workoutAverageFormModel->getActivity();
                $workoutFactory = WorkoutFactory::chooseFactory($activity->getType());
                $workout = $workoutFactory->create($workoutAverageFormModel);
                $em->persist($workout);
                $em->flush();

                $this->addFlash('success', 'Workout was created!! ');
                return $this->redirectToRoute('admin_workout_list');
            } else {
                $errors = $modelValidator->getErrors();
                return $this->render('admin_workout/add_average.html.twig', [
                    'workoutForm' => $formAverage->createView(),
                    'errors' => $errors,
                ]);
            }
        }

        return $this->render('admin_workout/add_average.html.twig', [
            'workoutForm' => $formAverage->createView(),
        ]);
    }

    /**
     * @Route("/admin/workout/add_specific", name="admin_workout_add_specific", methods={"POST", "GET"})
     */
    public function addSpecific(Request $request, EntityManagerInterface $em, WorkoutSpecificExtender $workoutSpecificExtender, ModelValidatorInterface $modelValidator)
    {
        $formSpecific = $this->createForm(WorkoutSpecificDataFormType::class, null, [
            'is_admin' => true
        ]);
    
        $formSpecific->handleRequest($request);
        
        if ($formSpecific->isSubmitted() && $formSpecific->isValid()) {
            $workoutSpecificModel = $formSpecific->getData();
            
            $workoutSpecificModel = $workoutSpecificExtender->fillWorkoutModel($workoutSpecificModel, null);

            if (!$workoutSpecificModel) {
                $this->addFlash('warning', 'Sorry we dont had activity matching your achievements in database');
                return $this->redirectToRoute('admin_workout_add_specific');
            }

            //Validation Model data
            $isValid = $modelValidator->isValid($workoutSpecificModel, ['model']);
            if ($isValid) {
                $workoutFactory = WorkoutFactory::chooseFactory($workoutSpecificModel->getType());
                $workout = $workoutFactory->create($workoutSpecificModel);

                $em->persist($workout);
                $em->flush();

                $this->addFlash('success', 'Workout was created!! ');
                return $this->redirectToRoute('admin_workout_list');
            } else {
                $errors = $modelValidator->getErrors();
                return $this->render('admin_workout/add_specific.html.twig', [
                    'workoutSpecificDataForm' => $formSpecific->createView(),
                    'errors' => $errors,
                ]);
            }
        }

        return $this->render('admin_workout/add_specific.html.twig', [
            'workoutSpecificDataForm' => $formSpecific->createView(),
        ]);
    }

    /**
     * @Route("/admin/workout/edit_average/{id}", name="admin_workout_edit", methods={"POST", "GET"})
     */
    public function editAverage(Workout $workout, Request $request, EntityManagerInterface $em, WorkoutAverageExtender $workoutAverageExtender, ModelValidatorInterface $modelValidator, WorkoutUpdaterInterface $workoutUpdater)
    {
        $this->denyAccessUnlessGranted('MANAGE', $workout);
        
        $activity = $workout->getActivity();

        $workoutModelFactory = WorkoutModelFactory::chooseFactory($activity->getType(), 'Average');
        $workoutAverageFormModel = $workoutModelFactory->create($workout);
        
        $formAverage = $this->createForm(WorkoutAverageDataFormType::class, $workoutAverageFormModel, [
            'is_admin' => true
        ]);

        $formAverage->handleRequest($request);
        if ($formAverage->isSubmitted() && $formAverage->isValid()) {
            //$workoutModel = $formAverage->getData(); //form handles modeldata so we don't need it
            $workoutAverageFormModel = $workoutAverageExtender->fillWorkoutModel($workoutAverageFormModel,null);

            //Validation Model data
            $isValid = $modelValidator->isValid($workoutAverageFormModel, ['model']);
            if ($isValid) {
                $workout = $workoutUpdater->update($workoutAverageFormModel, $workout);
                $em->persist($workout);
                $em->flush();

                $this->addFlash('success', 'Workout is updated!');

                return $this->redirectToRoute('admin_workout_edit', [
                    'id' => $workout->getId(),
                ]);
            } else {
                $errors = $modelValidator->getErrors();
                
                return $this->render('admin_workout/edit_average.html.twig', [
                    'workoutForm' => $formAverage->createView(),
                    'errors' => $errors,
                ]);
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
    public function editSpecific(Workout $workout, Request $request, EntityManagerInterface $em, WorkoutSpecificExtender $workoutSpecificExtender, ModelValidatorInterface $modelValidator, WorkoutUpdaterInterface $workoutUpdater)
    {
        $this->denyAccessUnlessGranted('MANAGE', $workout);

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
    
        $formSpecific->handleRequest($request);

        if ($formSpecific->isSubmitted() && $formSpecific->isValid()) {
            //$workoutModel = $formSpecific->getData(); //form handles modeldata so we don't need it
            $workoutSpecificFormModel = $workoutSpecificExtender->fillWorkoutModel($workoutSpecificFormModel,null);

            //Validation Model data
            $isValid = $modelValidator->isValid($workoutSpecificFormModel, ['model']);

            if ($isValid) {
                $workout = $workoutUpdater->update($workoutSpecificFormModel, $workout);
                $em->persist($workout);
                $em->flush();

                $this->addFlash('success', 'Workout is updated!');

                return $this->redirectToRoute('admin_workout_specific_edit', [
                    'id' => $workout->getId(),
                ]);
            } else {
                $errors = $modelValidator->getErrors();
                
                return $this->render('admin_workout/edit_specific.html.twig', [
                    'workoutSpecificDataForm' => $formSpecific->createView(),
                    'errors' => $errors,
                ]);
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

}

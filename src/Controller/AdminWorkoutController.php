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
use App\Form\WorkoutFormType;

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
    	$workoutQuery = $workoutRepository->findAllQuery();

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
     * @Route("/admin/workout/add", name="admin_workout_add", methods={"POST", "GET"})
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(WorkoutFormType::class, null, [
            'is_admin' => true
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $workout = new Workout();
            $workout = $form->getData();
            $workout->calculateSaveBurnoutEnergy();

            $em->persist($workout);
            $em->flush();

            $this->addFlash('success', 'Workout was created!! ');
            
            return $this->redirectToRoute('admin_workout_list');
        }

        return $this->render('admin_workout/add.html.twig', [
            'workoutForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/workout/edit/{id}", name="admin_workout_edit", methods={"POST", "GET"})
     */
    public function edit(Workout $workout, Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('MANAGE', $workout);

        $form = $this->createForm(WorkoutFormType::class, $workout, [
            'is_admin' => true
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $workout = $form->getData();
            $workout->calculateSaveBurnoutEnergy();

            $em->persist($workout);
            $em->flush();
            $this->addFlash('success', 'Workout is updated!');

            return $this->redirectToRoute('admin_workout_edit', [
                'id' => $workout->getId(),
            ]);
        }

        return $this->render('admin_workout/edit.html.twig', [
            'workoutForm' => $form->createView()
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

}

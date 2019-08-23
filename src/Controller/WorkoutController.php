<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Repository\WorkoutRepository;
use App\Entity\Workout;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\WorkoutFormType;

class WorkoutController extends AbstractController
{
    /**
     * @Route("/workout/list", name="workout_list")
     */
    public function list(WorkoutRepository $workoutRepository, Request $request, EntityManagerInterface $em)
    {
    	$user = $this->getUser();
    	$workouts = $workoutRepository->findBy(['user' => $user ]);


    	//forms

    	$form = $this->createForm(WorkoutFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $workout = new Workout();//zakomentować
            $workout = $form->getData();
            $workout->setUser($user);

            $em->persist($workout);
            $em->flush();

             $this->addFlash('success', 'Workout was created!! ');
            
            return $this->redirectToRoute('workout_list');
        }




        return $this->render('workout/list.html.twig', [
            'workouts' => $workouts,
            'workoutForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/workout/delete/{id}", name="workout_delete",  methods={"DELETE"})
     */
    public function delete(Request $req, Workout $workout, EntityManagerInterface $em)
    {
        $em->remove($workout);
        $em->flush();
    
        return new Response(null, 204);
    }
}

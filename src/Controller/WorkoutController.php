<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Repository\WorkoutRepository;
use App\Entity\Workout;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\WorkoutFormType;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Form\FormInterface;

class WorkoutController extends WorkoutUtilityController
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
     * @Route("/workout/add", name="workout_add_n", methods={"POST", "GET"})
     */
    public function add_n(Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(WorkoutFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $workout = new Workout();
            $workout = $form->getData();
            $workout->setUser($this->getUser());
            $burnoutEnergy = $this->calculateBurnoutEnergy($workout);
            $workout->setBurnoutEnergy($burnoutEnergy);

            $em->persist($workout);
            $em->flush();

            $this->addFlash('success', 'Workout was added!! ');
            
            return $this->redirectToRoute('workout_list');
        }


        return $this->render('workout/add.html.twig', [
            'workoutForm' => $form->createView(),
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

    /**
     * @Route("/api/workout/add", name="workout_add", methods={"POST"})
     */
    public function add(Request $request, EntityManagerInterface $em)
    {

        $data = json_decode($request->getContent(), true);

        if($data === null)
        {
            throw new BadRequestHttpException('Invalid Json');    
        }

        $form = $this->createForm(WorkoutFormType::class);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->json(
            $errors,
            400
            );
        }

        $workout = new Workout();
        $workout = $form->getData();
        $workout->setUser($this->getUser());

        $burnoutEnergy = $this->calculateBurnoutEnergy($workout);
        $workout->setBurnoutEnergy($burnoutEnergy);
        
        $em->persist($workout);
        $em->flush();

        $response = new Response(null, 201);

        $response->headers->set(
            'Location',
            $this->generateUrl('workout_get', ['id' => $workout->getId()])
        );
        
        return $response;
    }

     /**
     * @Route("/api/workout_get/{id}", name="workout_get", methods={"GET"})
     * 
     */
    public function getWorkoutAction(Workout $workout)
    {
        $duration = $workout->getDuration();
        $duration = date_format($duration, 'H:i');
        $workout->setTime($duration);

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
    public function edit(Workout $workout, Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);
        //dump(date_default_timezone_get());
        //dump($data);

        if($data === null)
        {
            throw new BadRequestHttpException('Invalid Json');    
        }

        //dump($workout);
        $form = $this->createForm(WorkoutFormType::class, $workout,
            ['csrf_protection' => false]);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->json(
            $errors,
            400
            );
        }
        //dump($form->getData());

        $workout = $form->getData();
        //dump($workout);

        $burnoutEnergy = $this->calculateBurnoutEnergy($workout);
        $workout->setBurnoutEnergy($burnoutEnergy);
        
        $em->persist($workout);
        $em->flush();

        $response = new Response(null, 201);

        $response->headers->set(
            'Location',
            $this->generateUrl('workout_get', ['id' => $workout->getId()])
        );

        return $response;
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

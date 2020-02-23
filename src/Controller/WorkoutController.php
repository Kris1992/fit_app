<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\WorkoutRepository;
use App\Entity\Workout;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\WorkoutFormType;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class WorkoutController extends AbstractController
{
    /**
     * @Route("/workout/list", name="workout_list")
     * @IsGranted("ROLE_USER")
     */
    public function list(WorkoutRepository $workoutRepository, Request $request, EntityManagerInterface $em)
    {

    	$user = $this->getUser();
    	$workouts = $workoutRepository->findBy(['user' => $user ]);

    	//forms

    	$form = $this->createForm(WorkoutFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $workout = new Workout();
            $workout = $form->getData();
            $workout->setUser($user);
            $workout->calculateSaveBurnoutEnergy();

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

        dump($workouts);
        
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
     * @Route("/workout/add", name="workout_add_n", methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function add_n(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(WorkoutFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $workout = new Workout();
            $workout = $form->getData();
            $workout->setUser($this->getUser());
            $workout->calculateSaveBurnoutEnergy();

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
        $this->denyAccessUnlessGranted('MANAGE', $workout);

        $em->remove($workout);
        $em->flush();
    
        return new Response(null, 204);
    }

    /**
     * @Route("/api/workout/add", name="workout_add", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);

        dump($data);

        if($data === null) {
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
        dump($form->getData());
        
        $workout = new Workout();
        $workout = $form->getData();
        $workout->setUser($this->getUser());
        $workout->calculateSaveBurnoutEnergy();
        
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
        //$durationSeconds = $workout->getDurationSeconds();
        //change date to own function
        //$durationSeconds = date('H:i:s', $durationSeconds);
        //$workout->setTime($durationSeconds);
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
    public function edit(Workout $workout, Request $request, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('MANAGE', $workout);

        $data = json_decode($request->getContent(), true);
        //dump(date_default_timezone_get());

        if($data === null) {
            throw new BadRequestHttpException('Invalid Json');    
        }

        $form = $this->createForm(WorkoutFormType::class, $workout);
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
        $workout->calculateSaveBurnoutEnergy();
        
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
     * @Route("/api/server_date", name="server_date_get", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * 
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

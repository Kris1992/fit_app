<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\WorkoutRepository;

class ExerciseController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(WorkoutRepository $workoutRepository)
    {
        $workouts = $workoutRepository->findWorkoutsFromLastWeek();
        dd($workouts);
        return $this->render('exercise/homepage.html.twig', [
            
        ]);
    }

}

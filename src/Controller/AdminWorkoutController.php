<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\WorkoutRepository;
use Knp\Component\Pager\PaginatorInterface;

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
}

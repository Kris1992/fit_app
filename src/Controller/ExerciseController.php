<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CuriosityRepository;

class ExerciseController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(CuriosityRepository $curiosityRepository)
    {
        $curiosities = $curiosityRepository->findPublishedOrderedByNewest(9);

        return $this->render('exercise/homepage.html.twig', [
            'curiosities' => $curiosities,
        ]);
    }

}

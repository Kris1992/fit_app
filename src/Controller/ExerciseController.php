<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ExerciseController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index()
    {


        return $this->render('exercise/homepage.html.twig', [
            
        ]);
    }
}

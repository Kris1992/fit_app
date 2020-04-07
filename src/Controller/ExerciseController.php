<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

//tests
use App\Services\ImagesManager\ImagesManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

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

    /**
     * @Route("/tests", name="app_tests")
     */
    public function tests(ImagesManagerInterface $imagesManager, string $uploadsDirectory)
    {
        $file = new File($uploadsDirectory.'/users_images/'.'prism.png');
        $f = $imagesManager->uploadImage($file, null, 100);
        return $this->render('exercise/homepage.html.twig', [
            
        ]);
    }


}

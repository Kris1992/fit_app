<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Curiosity;

class CuriosityController extends AbstractController
{
     /**
     * @Route("/curiosity/{slug}/show", name="curiosity_show", methods={"POST", "GET"})
     */
    public function show(Curiosity $curiosity, Request $request)
    {            
        
        return $this->render('curiosity/show.html.twig', [
            'curiosity' => $curiosity
        ]);
    }
}

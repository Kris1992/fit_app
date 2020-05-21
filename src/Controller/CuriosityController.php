<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Curiosity;

class CuriosityController extends AbstractController
{
     /**
     * @Route("/curiosity/{slug}/show", name="curiosity_show", methods={"POST", "GET"})
     */
    public function show(Curiosity $curiosity)
    {            
        
        return $this->render('curiosity/show.html.twig', [
            'curiosity' => $curiosity
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     */
    public function profile()
    {
        return $this->render('account/profile.html.twig', [
           
        ]);
    }
}

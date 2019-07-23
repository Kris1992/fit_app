<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/menu", name="admin_menu", methods={"GET"}))
     */
    public function menu()
    {
        return $this->render('admin/menu.html.twig', [
        ]);
    }
}

<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FriendController extends AbstractController
{
    /**
     * @Route("/friend/list", name="friend_list")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->render('friend/list.html.twig', [
        ]);
    }
}

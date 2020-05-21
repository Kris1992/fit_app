<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ChallengeRepository;

//is granted
class ChallengeController extends AbstractController
{

    /**
     * @Route("/challenge", name="challenge_list")
    */
    public function list(ChallengeRepository $challengeRepository)
    {
        //$challenges = $challengeRepository->getLastChallenges(3);

        return $this->render('challenge/list.html.twig', [
            'challenges' => $challenges
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\AbstractActivity;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityController extends AbstractController
{
    /**
     * @Route("/api/activity_get/{id}", name="activity_get", methods={"GET"})
     * 
     */
    public function getActivityAction(AbstractActivity $activity)
    {

        return $this->json(
            $activity,
            201,
            [],
            [
                'groups' => ['main']
            ]
        );
    }
}

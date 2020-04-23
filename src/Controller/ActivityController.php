<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\AbstractActivity;
use App\Repository\AbstractActivityRepository;

class ActivityController extends AbstractController
{
    /**
     * @Route("/api/activity_get/{id}", name="activity_get", methods={"GET"})
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

    /**
     * @Route("/api/activities", name="api_activity_get_all", methods={"POST", "GET"})
     */
    public function getAllActivitiesByNameAction(Request $request, AbstractActivityRepository $activityRepository)
    {
        $data = json_decode($request->getContent(), true);

        if($data === null) {
            throw new BadRequestHttpException('Invalid Json');    
        }

        $activities = $activityRepository->findBy(
            [
                'type' => $data['type'],
                'name' => $data['activityName']

            ]
        );

        if(!$activities) {
            $responseMessage = [
                'errorMessage' => 'This activity do not exist.'
            ];
            return new JsonResponse($responseMessage, Response::HTTP_BAD_REQUEST);
        }

        return $this->json(
            $activities,
            200,
            [],
            [
                'groups' => ['main']
            ]
        );
    }
}

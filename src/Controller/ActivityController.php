<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Exception\Api\ApiBadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\AbstractActivity;
use App\Repository\AbstractActivityRepository;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;
use App\Services\JsonErrorResponse\JsonErrorResponseTypes;

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
    public function getAllActivitiesByNameAction(Request $request, AbstractActivityRepository $activityRepository, JsonErrorResponseFactory $jsonErrorFactory)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $activities = $activityRepository->findBy(
            [
                'type' => $data['type'],
                'name' => $data['activityName']

            ]
        );

        if(!$activities) {
            return $jsonErrorFactory->createResponse(404, JsonErrorResponseTypes::TYPE_NOT_FOUND_ERROR, null, 'Activity not found.');
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

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\JsonErrorResponse\JsonErrorResponse;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;
use App\Services\Factory\ReactionModel\ReactionModelFactoryInterface;
use App\Services\Factory\Reaction\ReactionFactoryInterface;
use App\Services\ModelValidator\ModelValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\WorkoutRepository;
use App\Repository\ReactionRepository;
use App\Entity\Workout;

class ReactionUtilController extends AbstractController
{
    /**
     * @Route("/api/workout/{id}/reaction", name="api_workout_reaction", methods={"POST"} )
     */
    public function workoutReactionAction(Workout $workout, Request $request, ReactionModelFactoryInterface $reactionModelFactory, ReactionFactoryInterface $reactionFactory, ModelValidatorInterface $modelValidator, JsonErrorResponseFactory $jsonErrorFactory,EntityManagerInterface $entityManager, WorkoutRepository $workoutRepository, ReactionRepository $reactionRepository)
    {
        $reactionData = json_decode($request->getContent(), true);

        if($reactionData === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        /** @var User $user */
        $user = $this->getUser();

        //First check is reacted before
        $reactionOld = $reactionRepository->findOneBy([
            'workout' => $workout,
            'owner' => $user,
            'type' => $reactionData['type']

        ]);

        if ($reactionOld) {
            $workout->removeReaction($reactionOld);
        } else {
            $reactionModel = $reactionModelFactory->create($user, $workout, $reactionData['type']);

            //Validation Model data
            $isValid = $modelValidator->isValid($reactionModel);

            if(!$isValid) {
                $jsonError = new JsonErrorResponse(400, 
                    JsonErrorResponse::TYPE_MODEL_VALIDATION_ERROR,
                    $modelValidator->getErrorMessage()
                );

                return $jsonErrorFactory->createResponse($jsonError);
            }

            $reaction = $reactionFactory->create($reactionModel);
            $workout->addReaction($reaction);
        }

        $entityManager->persist($workout);
        $entityManager->flush();

        $reactionCount = $reactionRepository->countReactionsByWorkoutAndType(
            $workout,
            $reactionData['type']
        );

        return new JsonResponse(['count' => $reactionCount]);
    }

   
}

<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ChallengeRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Challenge;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;
use App\Services\JsonErrorResponse\JsonErrorResponseTypes;

/**
* @IsGranted("ROLE_USER")
**/
class ChallengeController extends AbstractController
{

    /**
     * @Route("/challenge", name="challenge_list", methods={"GET"})
    */
    public function list(ChallengeRepository $challengeRepository, Request $request, PaginatorInterface $paginator)
    {
        $searchTerms = $request->query->getAlnum('filterValue');
        $challengeQuery = $challengeRepository->findAllQuery($searchTerms);

        $pagination = $paginator->paginate(
            $challengeQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('perPage', 5)/*limit per page*/
        );

        return $this->render('challenge/list.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/challenge/{id}/show", name="challenge_show", methods={"POST", "GET"})
     */
    public function show(Challenge $challenge, Request $request, PaginatorInterface $paginator)
    {            

        //$challengeQuery = $challengeRepository->findCustomQuery();

        //$pagination = $paginator->paginate(
        //    $challengeQuery, /* query NOT result */
        //    $request->query->getInt('page', 1)/*page number*/,
        //    $request->query->getInt('perPage', 5)/*limit per page*/
        //);

        return $this->render('challenge/show.html.twig', [
            'challenge' => $challenge,
          //  'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/api/challenge/{id}/participate", name="api_challenge_participate", methods={"POST"})
     */
    public function participateAction(Challenge $challenge, EntityManagerInterface $entityManager, JsonErrorResponseFactory $jsonErrorFactory)
    {   

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $today = new \DateTime();
        if ($challenge->getStopAt() > $today) {
            $currentUser->addChallenge($challenge);
            $entityManager->flush();
            return new JsonResponse(null, Response::HTTP_OK);
        }
        
        return $jsonErrorFactory->createResponse(400, JsonErrorResponseTypes::TYPE_ACTION_FAILED, null, 'Challenge is ended.');
    }
}

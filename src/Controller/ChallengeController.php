<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ChallengeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

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
}

<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Services\JsonErrorResponse\JsonErrorResponse;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;
use App\Services\Factory\FriendInvitation\FriendInvitationFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;

use App\Repository\UserRepository;
use App\Repository\FriendRepository;


/**
* @IsGranted("ROLE_USER")
**/
class FriendController extends AbstractController
{
    /**
     * @Route("/friend/list", name="friend_list", methods={"GET"})
     */
    public function list(FriendRepository $friendRepository, PaginatorInterface $paginator, Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $searchTerms = $request->query->getAlnum('filterValue');
        $friendQuery = $friendRepository->findAllQueryByStatus($searchTerms, $currentUser, 'Accepted');

        $pagination = $paginator->paginate(
            $friendQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('perPage', 6)/*limit per page*/
        );

        $pagination->setCustomParameters([
            'placeholder' => 'Search in friends...'
        ]);
    
        return $this->render('friend/list.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/friend/requests", name="friend_requests", methods={"GET"})
     */
    public function requestlist(FriendRepository $friendRepository, Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $friendRequests = $friendRepository->findAllToAccept($currentUser);
    
        return $this->render('friend/requests_list.html.twig', [
            'friendRequests' => $friendRequests
        ]);
    }

    /**
     * @Route("/friend/search", name="friend_search", methods={"GET"})
     */
    public function search(RepositoryManagerInterface $finder, UserRepository $userRepository, PaginatorInterface $paginator, Request $request)
    {
        //$results = $finder->getRepository(User::class)->find('admin0@fit.com');

        //tymczasowo
        $searchTerms = $request->query->getAlnum('filterValue');
        if ($searchTerms) {
            $userQuery = $userRepository->findAllQuery($searchTerms);
        } else {
            $userQuery = [];
        }

        $pagination = $paginator->paginate(
            $userQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('perPage', 5)/*limit per page*/
        );

        $pagination->setCustomParameters([
            'placeholder' => 'Enter here e-mail, firstname, secondname or adress of your friend.'
        ]);

        return $this->render('friend/search.html.twig', [
            'pagination' => $pagination
        ]);
    }

    //Api
    /**
     * @Route("/api/friend/{id}/invite", name="api_friend_invite", methods={"GET"})
     */
    public function inviteAction(User $user, JsonErrorResponseFactory $jsonErrorFactory, FriendInvitationFactoryInterface $friendInvitationFactory, EntityManagerInterface $entityManager)
    {   
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($user !== $currentUser) {
            $friend = $friendInvitationFactory->create($currentUser, $user);
            $entityManager->persist($friend);
            $entityManager->flush();
            return new JsonResponse(Response::HTTP_OK);
        }

        $jsonError = new JsonErrorResponse(400, 
            JsonErrorResponse::TYPE_ACTION_FAILED,
            'Something goes wrong.'
        );

        return $jsonErrorFactory->createResponse($jsonError);
    }

}

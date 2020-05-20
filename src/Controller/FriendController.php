<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;
use App\Services\JsonErrorResponse\JsonErrorResponseTypes;
use App\Exception\Api\ApiBadRequestHttpException;
use App\Services\Factory\FriendInvitation\FriendInvitationFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\Updater\Friend\FriendUpdaterInterface;
use App\Entity\User;
use App\Entity\Friend;

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
    public function requestlist(FriendRepository $friendRepository)
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
    public function search(/*RepositoryManagerInterface $finder,*/ UserRepository $userRepository, PaginatorInterface $paginator, Request $request, FriendRepository $friendRepository)
    {   

        //$res = $finder->getRepository(User::class)->find('ell');
        
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $searchTerms = $request->query->getAlnum('filterValue');
        //$results = $finder->getRepository(User::class)->createPaginatorAdapter($searchTerms);

        //tymczasowo
        $userQuery = $userRepository->findAllQueryElastica($searchTerms, $currentUser);

        //z tym 6 calls i 8ms total 146 ms a po wyczyszczeniu cache 351 ms
        //Po wykorzystaniu kolekcji i joina zyskujemy na wydajności
        //$friendsRelationships = $friendRepository->findAllBetweenUserAndUsers($currentUser, $users);

        $pagination = $paginator->paginate(
            $userQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('perPage', 6)/*limit per page*/
        );

        $pagination->setCustomParameters([
            'placeholder' => 'Enter here e-mail, firstname, secondname or adress of your friend.'
        ]);

        return $this->render('friend/search.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    //Api
    /**
     * @Route("/api/friend/user/{id}/invite", name="api_friend_invite", methods={"GET"})
     */
    public function inviteAction(User $user, JsonErrorResponseFactory $jsonErrorFactory, FriendInvitationFactoryInterface $friendInvitationFactory, EntityManagerInterface $entityManager, FriendRepository $friendRepository)
    {   

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($user !== $currentUser) {
            //check is history of this realationship (I want keep just one friend object per pair)
            $oldStatus = $friendRepository->findAllBetweenUsers($currentUser, $user);
            if ($oldStatus) {
                $entityManager->remove($oldStatus);
            }

            $friend = $friendInvitationFactory->create($currentUser, $user);
            $entityManager->persist($friend);
            $entityManager->flush();
            return new JsonResponse(Response::HTTP_OK);
        }

        return $jsonErrorFactory->createResponse(400, JsonErrorResponseTypes::TYPE_ACTION_FAILED, null, 'Something goes wrong.');
    }

    /**
     * @Route("/api/friend/{id}/response", name="api_friend_response", methods={"POST", "GET"})
     */
    public function responseAction(Friend $friend, Request $request, JsonErrorResponseFactory $jsonErrorFactory, FriendUpdaterInterface $friendUpdater, EntityManagerInterface $entityManager): response
    {   
        $data = json_decode($request->getContent(), true);

        if($data === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($friend->getInvitee() === $currentUser) {
            $friend = $friendUpdater->update($friend, $data['status']);
            $entityManager->flush();
            return new JsonResponse(Response::HTTP_OK);
        }

        return $jsonErrorFactory->createResponse(400, JsonErrorResponseTypes::TYPE_ACTION_FAILED, null, 'Something goes wrong.');
    }

}

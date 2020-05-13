<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

/**
* @IsGranted("ROLE_ADMIN")
**/
class AdminUtilityController extends AbstractController
{
    /**
     * @Route("api/admin/utility/users", methods="GET", name="api_admin_utility_users")
     */
    public function getUsersAction(UserRepository $userRepository, Request $request)
    { 
        $emailMatcher = $request->query->get('query');
        $users = $userRepository->findAllMatching($emailMatcher);
        return $this->json(
            [
                'users' => $users
            ], 
            200, 
            [], 
            ['groups' => ['main']]
        );
    }

}

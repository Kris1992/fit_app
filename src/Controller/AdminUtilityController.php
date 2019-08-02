<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;

class AdminUtilityController extends AbstractController
{
    /**
     * @Route("/admin/utility/users", methods="GET", name="admin_utility_users")
     */
    public function getUsersApi(UserRepository $userRepository, Request $request)
    { 
        $users = $userRepository->findAllMatching($request->query->get('query'));
        return $this->json([
            'users' => $users
        ], 200, [], ['groups' => ['main']]);
    }
}

// IsGranted("ROLE_ADMIN_ARTICLE") 
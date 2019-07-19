<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;


use App\Form\UserRegistrationFormType;
use App\Form\Model\UserRegistrationFormModel;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/account", name="admin_account_list", methods={"GET"})
     */
    public function list(UserRepository $userRepository, PaginatorInterface $paginator, Request $request)
    {
    	$userQuery = $userRepository->findAllQuery();

        $pagination = $paginator->paginate(
            $userQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('perPage', 5)/*limit per page*/
        );
        
        return $this->render('admin_account/list.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * @Route("/admin/account/edit/{id}", name="admin_account_edit", methods={"POST", "GET"})
     */
    public function edit(User $user, Request $request, EntityManagerInterface $em, UserRegistrationFormModel $userModel)
    {
        
        //if ($user != $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {//change to voter soon
        //    throw $this->createAccessDeniedException('No access!');
        //}

        //transform user object to userModel object
        $userModel->setEmail($user->getEmail());
        $userModel->setFirstName($user->getFirstName());
        $userModel->setSecondName($user->getSecondName());
            
         
            

		$form = $this->createForm(UserRegistrationFormType::class, $userModel);
        /*$form = $this->createForm(ArticleFormType::class, $article, [
            'include_published_at' => true          //tak można sterować czy dane pole ma się znajdować w formie czy nie
        ]);*/
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            /** @var User $user */
            //$article = $form->getData(); tego już nie trzeba
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'User is updated!');
            //return $this->redirectToRoute('admin_article_list');
            return $this->redirectToRoute('admin_account_edit', [
                'id' => $user->getId(),
            ]);
        }
        return $this->render('admin_account/edit.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/account/delete/{id}", name="admin_account_delete",  methods={"DELETE"})
     */

    public function accountDelete(Request $req, User $user)//$id)
    {
        //$userRepository = $this->getDoctrine()->getRepository(User::class);
        //$user = $user_rep->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        

        $response = new Response();
        $this->addFlash('success','User was deleted!!');
        $response->send();
        return $response;

    }


}

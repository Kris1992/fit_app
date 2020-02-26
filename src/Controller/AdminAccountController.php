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
use App\Services\UploadImagesHelper;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
* @IsGranted("ROLE_ADMIN")
**/
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
    public function edit(User $user, Request $request, EntityManagerInterface $em, UserRegistrationFormModel $userModel, UploadImagesHelper $uploadImagesHelper)
    {
        //transform user object to userModel object
        /** @var User $user */
        $userModel->setId($user->getId());
        $userModel->setEmail($user->getEmail());
        $userModel->setFirstName($user->getFirstName());
        $userModel->setSecondName($user->getSecondName());
        $userModel->setGender($user->getGender());
        $userModel->setBirthdate($user->getBirthdate());
        $userModel->setWeight($user->getWeight());
        $userModel->setHeight($user->getHeight());
        $userModel->setRole($user->getRole());

        if($user->getImageFilename())
        {
            $userModel->setImageFilename($user->getImageFilename());
        }
        

            
        $form = $this->createForm(UserRegistrationFormType::class, $userModel, [
            'is_admin' => true
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $userModel2 = $form->getData();
        
            $user->setEmail($userModel2->getEmail());
            $user->setFirstName($userModel2->getFirstName());
            $user->setSecondName($userModel2->getSecondName());
            $user->setGender($userModel2->getGender());
            $user->setBirthdate($userModel2->getBirthdate());
            $user->setWeight($userModel2->getWeight());
            $user->setHeight($userModel2->getHeight());
            $user->setRoles([$userModel2->getRole()]);

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();

            if($uploadedFile)
            {
                $newFilename = $uploadImagesHelper->uploadUserImage($uploadedFile, null);

                $user->setImageFilename($newFilename);
            }
            
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'User is updated!');

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

    public function delete(Request $req, User $user)//$id)
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

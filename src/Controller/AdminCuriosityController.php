<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CuriosityRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Curiosity;
use Doctrine\ORM\EntityManagerInterface;

use App\Form\CuriosityFormType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
* @IsGranted("ROLE_ADMIN")
**/
class AdminCuriosityController extends AbstractController
{
	/**
     * @Route("/admin/curiosity", name="admin_curiosity_list", methods={"GET"})
     */
    public function list(CuriosityRepository $curiosityRepository, PaginatorInterface $paginator, Request $request)
    {
    	$curiosityQuery = $curiosityRepository->findAllQuery();

        $pagination = $paginator->paginate(
            $curiosityQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('perPage', 5)/*limit per page*/
        );
        
        return $this->render('admin_curiosity/list.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/curiosity/add", name="admin_curiosity_add", methods={"POST", "GET"})
     */
    public function add(Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(CuriosityFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

           	$curiosity = new Curiosity();
            $curiosity = $form->getData();

            $em->persist($curiosity);
            $em->flush();

            $this->addFlash('success', 'Curiosity was created!');
            
            return $this->redirectToRoute('admin_curiosity_list');
        }


        return $this->render('admin_curiosity/add.html.twig', [
            'curiosityForm' => $form->createView(),
        ]);
    }





    /**
     * @Route("/admin/curiosity/delete/{id}", name="admin_curiosity_delete",  methods={"DELETE"})
     */
    public function delete(Request $req, Curiosity $curiosity)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($curiosity);
        $entityManager->flush();

        
        $response = new Response();
        $this->addFlash('success','Curiosity was deleted!!');
        $response->send();
        return $response;
    }

}

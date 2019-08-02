<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ActivityRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ActivityFormType;

class AdminActivityController extends AbstractController
{
    /**
     * @Route("/admin/activity", name="admin_activity_list", methods={"GET"})
     */
    public function list(ActivityRepository $activityRepository, PaginatorInterface $paginator, Request $request)
    {

    	$activityQuery = $activityRepository->findAllQuery();

        $pagination = $paginator->paginate(
            $activityQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('perPage', 5)/*limit per page*/
        );

        return $this->render('admin_activity/list.html.twig', [
            'pagination' => $pagination
        ]);
    }


     /**
     * @Route("/admin/activity/add", name="admin_activity_add", methods={"POST", "GET"})
     */
    public function add(Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(ActivityFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

           	$activity = new Activity();//zakomentować
            $activity = $form->getData();

            $em->persist($activity);
            $em->flush();

            $this->addFlash('success', 'Activity was created!! ');
            
            return $this->redirectToRoute('admin_activity_list');
        }


        return $this->render('admin_activity/add.html.twig', [
            'activityForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/activity/edit/{id}", name="admin_activity_edit", methods={"POST", "GET"})
     */
    public function edit(Activity $activity, Request $request, EntityManagerInterface $em)
    {
        
        //if ($user != $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {//change to voter soon
        //    throw $this->createAccessDeniedException('No access!');
        //}
    	//dd($activity);
            
        $form = $this->createForm(ActivityFormType::class, $activity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $activity = $form->getData();

            $em->persist($activity);
            $em->flush();
            $this->addFlash('success', 'Activity is updated!');

            return $this->redirectToRoute('admin_activity_edit', [
                'id' => $activity->getId(),
            ]);
        }
        return $this->render('admin_activity/edit.html.twig', [
            'activityForm' => $form->createView()
        ]);
    }



    /**
     * @Route("/admin/activity/delete/{id}", name="admin_activity_delete",  methods={"DELETE"})
     */

    public function delete(Request $req, Activity $activity)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($activity);
        $entityManager->flush();

        $response = new Response();
        $this->addFlash('success','Activity was deleted!!');
        $response->send();
        return $response;

    }


}

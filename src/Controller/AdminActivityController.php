<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ActivityFormType;
use App\Entity\AbstractActivity;
use App\Services\Factory\ActivityFactory;
use App\Form\Model\Activity\BasicActivityFormModel;
use App\Repository\AbstractActivityRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
* @IsGranted("ROLE_ADMIN")
**/
class AdminActivityController extends AbstractController
{
    /**
     * @Route("/admin/activity", name="admin_activity_list", methods={"GET"})
     */
    public function list(AbstractActivityRepository $activityRepository, PaginatorInterface $paginator, Request $request)
    {

        $searchTerms = $request->query->getAlnum('filterValue');
        $activityQuery = $activityRepository->findAllQuery($searchTerms);

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

            $data = $form->getData();
            $activityFactory = ActivityFactory::chooseFactory($data['type']);
            $activity = $activityFactory->createActivity($data);

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
    public function edit(AbstractActivity $activity, Request $request, EntityManagerInterface $em)
    {            

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
    public function delete(Request $req, AbstractActivity $activity)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($activity);
        $entityManager->flush();

        $response = new Response();
        $this->addFlash('success','Activity was deleted!!');
        $response->send();
        return $response;
    }

    /**
     * @Route("/admin/activity/delete_selected", name="admin_activity_delete_selected",  methods={"POST", "DELETE"})
     */
    public function deleteSelected(Request $request,  EntityManagerInterface $entityManager, AbstractActivityRepository $activityRepository)
    {
        $submittedToken = $request->request->get('token');
        if($request->request->has('deleteId')){
            if ($this->isCsrfTokenValid('delete_multiple', $submittedToken)) {
                $ids = $request->request->get('deleteId');
                $activities = $activityRepository->findAllByIds($ids);
                if($activities){
                    foreach ($activities as $activity) {
                        $entityManager->remove($activity);
                    }
                    $entityManager->flush();

                    $this->addFlash('success','Activities were deleted!!');
                    return $this->redirectToRoute('admin_activity_list');
                }

                /* For now its not neccessary (admin can delete only 15 positions in one time)
                $batchSize = 10;
                $i = 1;
                foreach ($activities as $activity) {
                    $entityManager->remove($activity);
                    if (($i % $batchSize) === 0) {
                        $entityManager->flush();
                        $entityManager->clear();
                    }
                ++$i;
                }
                $entityManager->flush();
                */
               
            } else {
                $this->addFlash('danger','Wrong token');
                return $this->redirectToRoute('admin_activity_list');
            }
        }

        $this->addFlash('warning','Nothing to do');
        return $this->redirectToRoute('admin_activity_list');
    }

    /**
     * @Route("/admin/activity/specific_activity_form", name="admin_activity_specific_form")
     */
    public function getSpecificActivityForm(Request $request)
    {
        $type = $request->query->get('type');
        $activity = new BasicActivityFormModel();
        $activity->setType($type);
        $form = $this->createForm(ActivityFormType::class, $activity);
        
        return $this->render('forms/activity_specific_form.html.twig', [
            'activityForm' => $form->createView(),
        ]);
    }

}

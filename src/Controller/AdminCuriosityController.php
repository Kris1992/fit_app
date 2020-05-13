<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CuriosityRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Services\Factory\Curiosity\CuriosityFactoryInterface;
use App\Entity\Curiosity;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CuriosityFormType;
use App\Services\Factory\CuriosityModel\CuriosityModelFactoryInterface;
use App\Services\Updater\Curiosity\CuriosityUpdaterInterface;

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
        $searchTerms = $request->query->getAlnum('filterValue');
    	$curiosityQuery = $curiosityRepository->findAllQuery($searchTerms);

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
    public function add(Request $request, EntityManagerInterface $em, CuriosityFactoryInterface $curiosityFactory, string $tinymce_api_key)
    {
        $form = $this->createForm(CuriosityFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $curiosityModel = $form->getData();
            $user = $this->getUser();

            $curiosity = $curiosityFactory->create($curiosityModel, $user, $form['imageFile']->getData());

            $em->persist($curiosity);
            $em->flush();
            $this->addFlash('success', 'Curiosity was created!');
            
            return $this->redirectToRoute('admin_curiosity_list');
        }

        return $this->render('admin_curiosity/add.html.twig', [
            'curiosityForm' => $form->createView(),
            'tinymce_api_key' => $tinymce_api_key,
        ]);
    }

    /**
     * @Route("/admin/curiosity/{slug}/edit", name="admin_curiosity_edit", methods={"POST", "GET"})
     */
    public function edit(Curiosity $curiosity, Request $request, EntityManagerInterface $em, CuriosityModelFactoryInterface $curiosityModelFactory, CuriosityUpdaterInterface $curiosityUpdater, string $tinymce_api_key)
    {            
        
        $curiosityModel = $curiosityModelFactory->create($curiosity);

        $form = $this->createForm(CuriosityFormType::class, $curiosityModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {    
            $curiosity = $curiosityUpdater->update($curiosityModel, $curiosity, $form['imageFile']->getData());

            $em->flush();
            $this->addFlash('success', 'Curiosity was updated!');

            return $this->redirectToRoute('admin_curiosity_edit', [
                'slug' => $curiosity->getSlug(),
            ]);
        }

        return $this->render('admin_curiosity/edit.html.twig', [
            'curiosityForm' => $form->createView(),
            'tinymce_api_key' => $tinymce_api_key,
        ]);

    }

    /**
     * @Route("/admin/curiosity/{id}/delete", name="admin_curiosity_delete",  methods={"DELETE"})
     */
    public function delete(Request $req, Curiosity $curiosity)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($curiosity);
        $entityManager->flush();

        $response = new Response();
        $this->addFlash('success','Curiosity was deleted!');
        $response->send();
        return $response;
    }

     /**
     * @Route("/admin/curiosity/delete_selected", name="admin_curiosity_delete_selected",  methods={"POST", "DELETE"})
     */
    public function deleteSelected(Request $request, EntityManagerInterface $entityManager, CuriosityRepository $curiosityRepository)
    {

        $submittedToken = $request->request->get('token');
        if($request->request->has('deleteId')) {
            if ($this->isCsrfTokenValid('delete_multiple', $submittedToken)) {
                $ids = $request->request->get('deleteId');
                $curiosities = $curiosityRepository->findAllByIds($ids);
                if($curiosities) {
                    foreach ($curiosities as $curiosity) {
                        $entityManager->remove($curiosity);
                    }
                    $entityManager->flush();

                    $this->addFlash('success','Curiosities were deleted!');
                    return $this->redirectToRoute('admin_curiosity_list');
                }
            } else {
                $this->addFlash('danger','Wrong token.');
                return $this->redirectToRoute('admin_curiosity_list');
            }
        }

        $this->addFlash('warning','Nothing to do.');
        return $this->redirectToRoute('admin_curiosity_list');
    }

}

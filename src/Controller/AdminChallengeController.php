<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ChallengeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Challenge;
use App\Form\ChallengeFormType;
use App\Form\Model\Challenge\ChallengeFormModel;
use App\Services\Factory\Challenge\ChallengeFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Factory\ChallengeModel\ChallengeModelFactoryInterface;
use App\Services\Updater\Challenge\ChallengeUpdaterInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
/**
* @IsGranted("ROLE_ADMIN")
**/
class AdminChallengeController extends AbstractController
{
    /**
     * @Route("/admin/challenge", name="admin_challenge_list")
     */
    public function list(ChallengeRepository $challengeRepository, PaginatorInterface $paginator, Request $request)
    {
        $searchTerms = $request->query->getAlnum('filterValue');
        $challengeQuery = $challengeRepository->findAllQuery($searchTerms);

        $pagination = $paginator->paginate(
            $challengeQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('perPage', 5)/*limit per page*/
        );

        return $this->render('admin_challenge/list.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/challenge/add", name="admin_challenge_add", methods={"POST", "GET"})
     */
    public function add(Request $request, EntityManagerInterface $entityManager, ChallengeFactoryInterface $challengeFactory)
    {

        $form = $this->createForm(ChallengeFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $challengeModel = $form->getData();       
            $challenge = $challengeFactory->create($challengeModel);

            $entityManager->persist($challenge);
            $entityManager->flush();

            $this->addFlash('success', 'Challenge was created!');
            return $this->redirectToRoute('admin_challenge_list');
        }

        return $this->render('admin_challenge/add.html.twig', [
            'challengeForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/challenge/{id}/edit", name="admin_challenge_edit", methods={"POST", "GET"})
     */
    public function edit(Challenge $challenge, Request $request, EntityManagerInterface $entityManager, ChallengeModelFactoryInterface $challengeModelFactory, ChallengeUpdaterInterface $challengeUpdater)
    {            
        $this->denyAccessUnlessGranted('MANAGE', $challenge);
        $challengeModel = $challengeModelFactory->create($challenge);

        $form = $this->createForm(ChallengeFormType::class, $challengeModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {    
            $challenge = $challengeUpdater->update($challengeModel, $challenge);

            $entityManager->flush();
            $this->addFlash('success', 'Challenge is updated!');

            return $this->redirectToRoute('admin_challenge_edit', [
                'id' => $challenge->getId(),
            ]);
        }

        return $this->render('admin_challenge/edit.html.twig', [
            'challengeForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/challenge/activity_name_select", name="admin_challenge_activity_select")
     */
    public function getSpecificActivityNameSelect(Request $request)
    {   
        $type = $request->query->get('activityType');
        if ($type === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $challengeModel = new ChallengeFormModel();
        $challengeModel->setActivityType($type);
        $form = $this->createForm(ChallengeFormType::class, $challengeModel);
        
        return $this->render('forms/activity_select_form.html.twig', [
            'challengeForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/challenge/{id}/delete", name="admin_challenge_delete",  methods={"DELETE"})
     */
    public function delete(Request $request, Challenge $challenge, EntityManagerInterface $entityManager)
    {
        $today = new \DateTime();
        if ($challenge->getStartAt() < $today && $challenge->getStopAt() > $today) {
            $response = new Response(null, Response::HTTP_METHOD_NOT_ALLOWED);
            $this->addFlash('danger','Challenge is in progress. You can not delete it now!');
            $response->send();
            return $response;
        }
        
        $entityManager->remove($challenge);
        $entityManager->flush();

        $response = new Response();
        $this->addFlash('success','Challenge was deleted!');
        $response->send();
        return $response;
    }

    /**
     * @Route("/admin/challenge/delete_selected", name="admin_challenge_delete_selected",  methods={"POST", "DELETE"})
     */
    public function deleteSelected(Request $request, EntityManagerInterface $entityManager, ChallengeRepository $challengeRepository)
    {
        $submittedToken = $request->request->get('token');
        if($request->request->has('deleteId')) {
            if ($this->isCsrfTokenValid('delete_multiple', $submittedToken)) {
                $ids = $request->request->get('deleteId');
                $challenges = $challengeRepository->findAllByIds($ids);
                if($challenges) {
                    foreach ($challenges as $challenge) {
                        $entityManager->remove($challenge);
                    }
                    $entityManager->flush();

                    $this->addFlash('success','Challenges were deleted!');
                    return $this->redirectToRoute('admin_challenge_list');
                }

            } else {
                $this->addFlash('danger','Wrong token.');
                return $this->redirectToRoute('admin_challenge_list');
            }
        }

        $this->addFlash('warning','Nothing to do.');
        return $this->redirectToRoute('admin_challenge_list');
    }
}

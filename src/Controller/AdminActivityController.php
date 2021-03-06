<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ActivityFormType;
use App\Form\Model\CSVFile\CSVFileFormModel;
use App\Entity\AbstractActivity;
use App\Services\Factory\Activity\ActivityFactory;
use App\Form\Model\Activity\BasicActivityFormModel;
use App\Repository\AbstractActivityRepository;
use App\Exception\Api\ApiBadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Services\Transformer\Activity\ActivityTransformer;
use App\Services\ModelValidator\ModelValidatorInterface;
use App\Services\ActivitiesImporter\ActivitiesImporterInterface;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;
use App\Services\JsonErrorResponse\JsonErrorResponseTypes;
use App\Services\FilesManager\FilesManagerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
    public function add(Request $request, EntityManagerInterface $entityManager, ModelValidatorInterface $modelValidator)
    {

        $form = $this->createForm(ActivityFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            //Form don't let not valid type, but if developer forget about implement it in transformers or factory it will throw exception
            try {
                $activityTransformer = ActivityTransformer::chooseTransformer($data['type']);
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());

                return $this->render('admin_activity/add.html.twig', [
                    'activityForm' => $form->createView(),
                ]);
            }
            
            $activityModel = $activityTransformer->transformArrayToModel($data);
            
            //Validation Model data
            $isValid = $modelValidator->isValid($activityModel);
            if($isValid) {
                try {
                    $activityFactory = ActivityFactory::chooseFactory($activityModel->getType());
                    $activity = $activityFactory->create($activityModel);

                    $entityManager->persist($activity);
                    $entityManager->flush();

                    $this->addFlash('success', 'Activity was created!');
            
                    return $this->redirectToRoute('admin_activity_list');
                } catch (\Exception $e) {
                    $this->addFlash('warning', $e->getMessage());
                }
            } else {
                $modelValidator->mapErrorsToForm($form);
            }
        }

        return $this->render('admin_activity/add.html.twig', [
            'activityForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("api/admin/activity/import", name="api_admin_activity_import", methods={"POST", "GET"})
     */
    public function import(Request $request, ModelValidatorInterface $modelValidator, ActivitiesImporterInterface $activitiesImporter, JsonErrorResponseFactory $jsonErrorFactory)
    {

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('activityCSVFile');
        if ($uploadedFile === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');
        }

        $CSVFileFormModel = new CSVFileFormModel();
        $CSVFileFormModel->setUploadedFile($uploadedFile);

        //Validation Model data
        $isValid = $modelValidator->isValid($CSVFileFormModel);

        if(!$isValid) {
            return $jsonErrorFactory->createResponse(
                400, 
                JsonErrorResponseTypes::TYPE_MODEL_VALIDATION_ERROR, 
                null, 
                $modelValidator->getErrorMessage()
            );
        }
        
        try {
            $result = $activitiesImporter->import($CSVFileFormModel->getUploadedFile());
        } catch (\Exception $e) {
            return $jsonErrorFactory->createResponse(
                400, 
                JsonErrorResponseTypes::TYPE_ACTION_FAILED, 
                null, 
                $e->getMessage()
            );
        }

        return new JsonResponse($result, Response::HTTP_OK);
    }

     /**
     * @Route("/admin/activity/{id}/edit", name="admin_activity_edit", methods={"POST", "GET"})
     */
    public function edit(AbstractActivity $activity, Request $request, EntityManagerInterface $entityManager, ModelValidatorInterface $modelValidator)
    {            
        //An entity should be always valid !! so I dont wanna bind to form activity object
        //$form = $this->createForm(ActivityFormType::class, $activity);
        
        $activityTransformer = ActivityTransformer::chooseTransformer($activity->getType());
        $activityModel = $activityTransformer->transformToModel($activity);

        $form = $this->createForm(ActivityFormType::class, $activityModel);

        $form->handleRequest($request);
        if($request->isMethod('POST')) {
            //Validation Model data
            $isValid = $modelValidator->isValid($activityModel);
            if(!$isValid) {
                $modelValidator->mapErrorsToForm($form);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {    
            $activity = $activityTransformer->transformToActivity($activityModel, $activity);

            $entityManager->flush();
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
     * @Route("/admin/activity/{id}/delete", name="admin_activity_delete",  methods={"DELETE"})
     */
    public function delete(AbstractActivity $activity)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($activity);
        $entityManager->flush();

        $response = new Response();
        $this->addFlash('success','Activity was deleted!');
        $response->send();
        return $response;
    }

    /**
     * @Route("/admin/activity/delete_selected", name="admin_activity_delete_selected",  methods={"POST", "DELETE"})
     */
    public function deleteSelected(Request $request, EntityManagerInterface $entityManager, AbstractActivityRepository $activityRepository)
    {
        $submittedToken = $request->request->get('token');
        if($request->request->has('deleteId')) {
            if ($this->isCsrfTokenValid('delete_multiple', $submittedToken)) {
                $ids = $request->request->get('deleteId');
                $activities = $activityRepository->findAllByIds($ids);
                if($activities) {
                    foreach ($activities as $activity) {
                        $entityManager->remove($activity);
                    }
                    $entityManager->flush();

                    $this->addFlash('success','Activities were deleted!');
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
                $this->addFlash('danger','Wrong token.');
                return $this->redirectToRoute('admin_activity_list');
            }
        }

        $this->addFlash('warning','Nothing to do.');
        return $this->redirectToRoute('admin_activity_list');
    }

    /**
     * @Route("/admin/activity/specific_activity_form", name="admin_activity_specific_form")
     */
    public function getSpecificActivityForm(Request $request)
    {
        $type = $request->query->get('type');

        if ($type === null) {
            throw new ApiBadRequestHttpException('Invalid JSON.');    
        }

        $activity = new BasicActivityFormModel();
        $activity->setType($type);
        $form = $this->createForm(ActivityFormType::class, $activity);
        
        return $this->render('forms/activity_specific_form.html.twig', [
            'activityForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/api/admin/activity/download_template", 
     * name="api_admin_activity_download_template", methods={"GET"})
     */
    public function downloadActivitiesTemplate(Request $request, FilesManagerInterface $filesManager)
    {
        $templatePath = '/templates/activitiesTemplate.csv';
        $absolutePath = $filesManager->getAbsolutePath($templatePath);
        
        return $this->file($absolutePath, 'template.csv', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

}

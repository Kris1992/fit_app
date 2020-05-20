<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Services\AttachmentsManager\AttachmentsManagerInterface;
use App\Services\Factory\AttachmentModel\AttachmentModelFactoryInterface;
use App\Services\ModelValidator\ModelValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\Api\ApiBadRequestHttpException;
use App\Services\Factory\Attachment\AttachmentFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;
use App\Services\JsonErrorResponse\JsonErrorResponseTypes;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Envelope;
use App\Message\Command\CheckIsAttachmentUsed;

/**
* @IsGranted("ROLE_ADMIN")
**/
class AdminAttachmentController extends AbstractController
{
    /**
     * @Route("api/admin/attachment", name="api_admin_attachment")
     */
    public function uploadAction(Request $request, AttachmentsManagerInterface $attachmentsManager, AttachmentModelFactoryInterface $attachmentModelFactory, ModelValidatorInterface $modelValidator, AttachmentFactoryInterface $attachmentFactory, EntityManagerInterface $entityManager, MessageBusInterface $messageBus, JsonErrorResponseFactory $jsonErrorFactory)
    {

        /**
         * @var User $user
         */
        $user = $this->getUser();

        $file = $request->files->get('file');
        if (!$file) {
            throw new ApiBadRequestHttpException('Invalid JSON.');
        }

        $pathAndFilename = $attachmentsManager->upload($file, $user->getLogin());
        if ($pathAndFilename) {
            $attachmentModel = $attachmentModelFactory->createFromData($pathAndFilename['filename']);
            $isValid = $modelValidator->isValid($attachmentModel);

            if ($isValid) {
                $attachment = $attachmentFactory->create($attachmentModel);

                $entityManager->persist($attachment);
                $entityManager->flush();

                //If image will be not handled by curiosity (delete after 1 hour)
                $message = new CheckIsAttachmentUsed(
                    $attachment->getId(),
                    $user->getLogin()
                );
                $envelope = new Envelope($message, [
                    new DelayStamp(3600000)//1 hour delay 
                ]);
                $messageBus->dispatch($envelope);

                return new JsonResponse(
                    [
                        'location' => $pathAndFilename['partialPath'] 
                    ],
                    Response::HTTP_OK);
            }
        }

        return $jsonErrorFactory->createResponse(400, JsonErrorResponseTypes::TYPE_ACTION_FAILED, null, 'Wrong data given.');
    }
}

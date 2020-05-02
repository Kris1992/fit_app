<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use App\Exception\Api\ApiBadRequestHttpException;
use App\Services\JsonErrorResponse\JsonErrorResponseFactory;
use App\Services\JsonErrorResponse\JsonErrorResponse;

/*
 ApiExceptionSubscriber will handle all api exceptions and transform it to JsonErrorResponse
 */
class ApiExceptionSubscriber implements EventSubscriberInterface
{

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof ApiBadRequestHttpException) {

            $jsonErrorResponse = new JsonErrorResponse($exception->getStatusCode(), JsonErrorResponse::TYPE_INVALID_REQUEST_BODY_FORMAT, null);
            $jsonErrorResponse->setSingleExtraData('detail', $exception->getMessage());
            $factory = new JsonErrorResponseFactory();
            
            $response = $factory->createResponse($jsonErrorResponse);
            $event->setResponse($response);
        }

        return;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}

<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiCsrfValidationSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();


        // no validation needed on safe methods
        if ($request->isMethodSafe(false)) {
            return;
        }

        if (!$request->attributes->get('is_api')) {
            return;
        }
        //dump($request->attributes->all());die;
        if ($request->headers->get('Content-Type') != 'application/json') {
            $response = new JsonResponse([
                'errorMessage' => 'Invalid Content-Type'
            ], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
            
            $event->setResponse($response);
            return;
        }
    }
    
    public static function getSubscribedEvents()
    {
        return [
           'kernel.request' => 'onKernelRequest',
        ];
    }
}
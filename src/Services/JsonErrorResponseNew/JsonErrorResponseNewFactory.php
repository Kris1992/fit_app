<?php

namespace App\Services\JsonErrorResponseNew;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Creates JsonErrorResponse object
 */
class JsonErrorResponseNewFactory
{
    public function createResponse(JsonErrorResponseNew $jsonErrorResponse)
    {
        $data = $jsonErrorResponse->toArray();
        $response = new JsonResponse(
            $data,
            $jsonErrorResponse->getStatusCode()
        );
        $response->headers->set('Content-Type', 'application/problem+json');

        return $response;
    }
}


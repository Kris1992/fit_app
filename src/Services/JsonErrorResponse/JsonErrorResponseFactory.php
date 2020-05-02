<?php

namespace App\Services\JsonErrorResponse;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Creates JsonErrorResponse object
 */
class JsonErrorResponseFactory 
{
    public function createResponse(JsonErrorResponse $jsonErrorResponse)
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


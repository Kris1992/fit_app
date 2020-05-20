<?php

namespace App\Services\JsonErrorResponse;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Creates JsonErrorResponse object
 */
class JsonErrorResponseFactory
{

    public function createResponse(int $statusCode, string $type, $data = null, $customTitle = null): JsonResponse
    {
        // tu stworzyć JsonErrorResponse
        //Data is most important if someone pass data and customTitle 
        if ($data) {
            $jsonError = new JsonErrorResponse($statusCode, $type, null);
            $jsonError->setArrayExtraData($data);// single czy array możemy to sprawdzić data[1]
        } else {
            $jsonError = new JsonErrorResponse($statusCode, $type, $customTitle);
        }

        $data = $jsonError->toArray();

        $response = new JsonResponse(
            $data,
            $jsonError->getStatusCode()
        );
        $response->headers->set('Content-Type', 'application/problem+json');

        return $response;
    }
}

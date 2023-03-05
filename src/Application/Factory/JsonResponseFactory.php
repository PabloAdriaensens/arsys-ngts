<?php

namespace App\Application\Factory;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonResponseFactory
{
    /**
     * @param array $data
     * @return JsonResponse
     */
    public function success(array $data = []): JsonResponse
    {
        return new JsonResponse(
            $data
        );
    }

    /**
     * @param string $message
     * @param int $errorCode
     * @return JsonResponse
     */
    public function error(string $message = 'Unexpected internal server error.', int $errorCode = 500): JsonResponse
    {
        return new JsonResponse(
            $message,
            $errorCode
        );
    }
}
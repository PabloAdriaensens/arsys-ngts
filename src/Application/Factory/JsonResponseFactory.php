<?php

namespace App\Application\Factory;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseFactory
{
    /**
     * @param array $data
     * @return JsonResponse
     */
    public function success(array $data = []): JsonResponse
    {
        $responseObject = [
            'success' => true,
        ];

        if (!empty($data)) {
            $responseObject['data'] = $data;
        }

        return new JsonResponse(
            json_encode($responseObject, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            JsonResponse::HTTP_OK,
            ['Content-Type' => 'application/json; charset=utf-8'],
            true
        );
    }

    /**
     * @param string $message
     * @param int $errorCode
     * @return JsonResponse
     */
    public function error(
        string $message = 'Unexpected internal server error.',
        int $errorCode = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        return new JsonResponse(
            [
                'success' => false,
                'message' => $message,
            ],
            $errorCode
        );
    }
}
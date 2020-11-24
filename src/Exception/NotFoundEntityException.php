<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;

class NotFoundEntityException extends \Exception
{
    /**
     * @return JsonResponse
     */
    public function toJsonResponse() : JsonResponse
    {
        return new JsonResponse([
            'error' => [
                'message' => sprintf('The requested resource is not found.'),
                'code' => JsonResponse::HTTP_UNAUTHORIZED,
            ]], JsonResponse::HTTP_UNAUTHORIZED
        );
    }
}
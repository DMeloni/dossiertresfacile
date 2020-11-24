<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;

class InternalErrorException extends \Exception
{
    /**
     * @return JsonResponse
     */
    public static function toJsonResponse() : JsonResponse
    {
        return new JsonResponse([
            'error' => [
                'message' => sprintf('The server cannot do this.'),
                'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            ]], JsonResponse::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
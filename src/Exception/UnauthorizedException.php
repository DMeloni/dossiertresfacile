<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;

class UnauthorizedException extends \Exception
{
    /**
     * @return JsonResponse
     */
    public static function toJsonResponse() : JsonResponse
    {
        return new JsonResponse([
            'error' => [
                'message' => "You cannot do this.",
                'code' => JsonResponse::HTTP_NOT_FOUND,
            ]], JsonResponse::HTTP_NOT_FOUND
        );
    }
}
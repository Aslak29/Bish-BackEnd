<?php

namespace App\GlobalFunction;

use Symfony\Component\HttpFoundation\JsonResponse;

class FunctionErrors
{
    public function generateCodeError(string $numError, string $message, int $status):JsonResponse
    {
        return new JsonResponse([
            "errorCode" => ".$numError.",
            "errorMessage" => ".$message."
        ], $status);
    }

    public function generateCodeError004():JsonResponse
    {
        return new JsonResponse([
            "errorCode" => "004",
            "errorMessage" => "is_trend is not boolean !"
        ], 406);
    }
}

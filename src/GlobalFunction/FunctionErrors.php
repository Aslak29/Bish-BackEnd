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

    public function generateCodeError003():JsonResponse
    {
        return new JsonResponse([
            "errorCode" => "003",
            "errorMessage" => "la catégorie n'éxiste pas !"
        ], 404);
    }
    public function generateCodeError004():JsonResponse
    {
        return new JsonResponse([
            "errorCode" => "004",
            "errorMessage" => "is_trend is not boolean !"
        ], 406);
    }

    public function generateCodeError005():JsonResponse
    {
        return new JsonResponse([
            "errorCode" => "005",
            "errorMessage" => "is_available is not boolean !"
        ], 406);
    }

    public function generateCodeError006():JsonResponse
    {
        return new JsonResponse([
            "errorCode" => "006",
            "errorMessage" => "isFinish is not boolean !"
        ], 406);
    }

    public function generateCodeError007():JsonResponse
    {
        return new JsonResponse([
            "errorCode" => "007",
            "errorMessage" => "le contact n'éxiste pas !"
        ], 404);
    }

}

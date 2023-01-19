<?php

namespace App\Services;

use App\Repository\CodePromoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class CodePromoService
{
    private CodePromoRepository $codePromoRepository;

    /**
     * @param CodePromoRepository $codePromoRepository
     */
    public function __construct(CodePromoRepository $codePromoRepository)
    {
        $this->codePromoRepository = $codePromoRepository;
    }

    public function findAll(): JsonResponse
    {
        $code = $this->codePromoRepository->findAll();
        $codeArray = [];

        if ($code) {
            foreach ($code as $codePromo) {
                $codeArray[] = [
                    "name" => $codePromo->getName(),
                    "remise" => $codePromo->getRemise(),
                    "montantMin" => $codePromo->getMontantMinimum(),
                    "type" => $codePromo->getType(),
                    "startDate" => [
                        "date" => $codePromo->getStartDate()->format("d-m-Y"),
                        "time" => $codePromo->getStartDate()->format("H:m:s")
                    ],
                    "endDate" => [
                        "date" => $codePromo->getEndDate()->format("d-m-Y"),
                        "time" => $codePromo->getEndDate()->format("H:m:s")
                    ],
                ];
            }
        }


        return new JsonResponse($codeArray, 200);
    }
}
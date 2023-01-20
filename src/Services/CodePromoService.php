<?php

namespace App\Services;

use App\Entity\CodePromo;
use App\Repository\CodePromoRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;

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
                    "id" => $codePromo->getId(),
                    "name" => $codePromo->getName(),
                    "remise" => $codePromo->getRemise(),
                    "montantMin" => $codePromo->getMontantMinimum(),
                    "type" => $codePromo->getType(),
                    "startDateEN" => $codePromo->getStartDate()->format("Y-m-d"),
                    "endDateEN" => $codePromo->getEndDate()->format("Y-m-d"),
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

    /**
     * @throws Exception
     */
    public function create($data): JsonResponse
    {
        $codeVerif = $this->codePromoRepository->findAll();
        $tabCodePromo = [];

        foreach ($codeVerif as $cv) {
            $tabCodePromo[] = $cv->getName();
        }

        if (in_array($data["name"], $tabCodePromo)) {
            return new JsonResponse([
                "errorCode" => "035",
                "errorMessage" => "Le nom de cette promotion est déjà utiliser !"
            ], 404);
        }

        $codeArray = [];
        if (!empty($data)) {
            $code = new CodePromo();
            $code->setName($data["name"]);
            $code->setRemise($data["remise"]);
            $code->setMontantMinimum($data["montantMin"]);
            $startDate = new \DateTime($data["startDate"]);
            $code->setStartDate($startDate);
            $endDate = new \DateTime($data["endDate"]);
            $code->setEndDate($endDate);
            $code->setType($data["type"]);

            $this->codePromoRepository->save($code, true);

            $codeArray[] = [
                "id" => $code->getId(),
                "name" => $code->getName()
            ];
        }else {
            return new JsonResponse([
                "errorCode" => "030",
                "errorMessage" => "Les données attendue ne sont pas correct !"
            ], 404);
        }

        return new JsonResponse($codeArray, 200);
    }

    /**
     * @throws Exception
     */
    public function update($data): JsonResponse
    {
        $codeArray = [];
        $code = $this->codePromoRepository->find($data["id"]);

        if (!empty($data)) {
            $code->setName($data["name"]);
            $code->setRemise($data["remise"]);
            $code->setMontantMinimum($data["montantMin"]);
            $startDate = new \DateTime($data["startDate"]);
            $code->setStartDate($startDate);
            $endDate = new \DateTime($data["endDate"]);
            $code->setEndDate($endDate);
            $code->setType($data["type"]);
            $this->codePromoRepository->save($code, true);
        }else {
            return new JsonResponse([
                "errorCode" => "030",
                "errorMessage" => "Les données attendue ne sont pas correct !"
            ], 404);
        }

        return new JsonResponse($codeArray, 200);
    }

    public function delete($id): JsonResponse
    {
        $code = $this->codePromoRepository->find($id);

        if ($code) {
            $this->codePromoRepository->remove($code,true);
        }else {
            return new JsonResponse([
                "errorCode" => "030",
                "errorMessage" => "Le Code Promo n'existe pas !"
            ], 404);
        }
        return new JsonResponse(null, 200);
    }

    public function multipleRemoveCodePromo($data): JsonResponse
    {
        foreach ($data as $id) {
            $code = $this->codePromoRepository->find($id);

            if (!$code) {
                return new JsonResponse([
                    "errorCode" => "A définir",
                    "errorMessage" => "Le code promo n'éxiste pas !"
                ]);
            }

            $this->codePromoRepository->remove($code, true);
        }
        return new JsonResponse(null, 200);
    }

}

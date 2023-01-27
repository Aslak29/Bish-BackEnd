<?php

namespace App\Controller;

use App\Repository\ProduitBySizeRepository;
use App\Repository\TailleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
class SizeController extends AbstractController
{

    /**
     * @param TailleRepository $tailleRepository
     * @return JsonResponse
     * @OA\Tag (name="Taille")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('api/public/size/', name: 'app_size', methods: "GET")]
    public function findAll(TailleRepository $tailleRepository):JsonResponse
    {
        $tailles = $tailleRepository->findAll();
        $arrayTailles = [];
        foreach ($tailles as $taille) {
            $arrayTailles[] = [
                "id" => $taille->getId(),
                "taille" => $taille->getTaille(),
                "type" => $taille->getType()
            ];
        }
        return new JsonResponse($arrayTailles, 200);
    }

    /**
     * @param ProduitBySizeRepository $produitBySizeRepo
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Taille")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('api/public/size/allSizeProduct/{idProduct}', name: 'app_size_allSize', methods: "GET")]
    public function allSizeProduct(ProduitBySizeRepository $produitBySizeRepo, Request $request): JsonResponse
    {
        $productBySize = $produitBySizeRepo->findAllStockByIdProduct($request->attributes->get('idProduct'));

        $allSizeProductArray = [];
        foreach ($productBySize as $oneSizeProduct) {
            $allSizeProductArray[ $oneSizeProduct->getTaille()->getTaille()] = [
                "stock" => $oneSizeProduct->getStock()
            ];
        }
        return new JsonResponse($allSizeProductArray, 200);
    }


    /**
     * @param TailleRepository $tailleRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Taille")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('api/public/size/typeTaille/{typeTaille}', name: 'app_type_size', methods: "GET")]
    public function typeTaille(TailleRepository $tailleRepository, Request $request): JsonResponse
    {
        if ($request->attributes->get('typeTaille')) {
            $typeTailles = $tailleRepository->findTypeSyze($request->attributes->get('typeTaille'));
        }else {
            $typeTailles = $tailleRepository->findTypeSyze("Adulte");
        }
        $arrayJson = [
            "tailles" => []
        ];
        foreach ($typeTailles as $typeTaille) {
            $arrayJson["tailles"][] =[
                "size" => $typeTaille->getTaille(),
            ];
        }

        return new JsonResponse($arrayJson, 200);
    }
}


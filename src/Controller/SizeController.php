<?php

namespace App\Controller;

use App\Repository\ProduitBySizeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;

#[Route('/api/size/')]

class SizeController extends AbstractController
{
    
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
    #[Route('/allSizeProduct/{idProduct}', name: 'app_size', methods: "GET")]
    public function allSizeProduct(ProduitBySizeRepository $produitBySizeRepo, Request $request): JsonResponse{
        $productBySize = $produitBySizeRepo->findBy(["produit" => $request->attributes->get('idProduct')]);
        $allSizeProductArray = [];
        foreach($productBySize as $oneSizeProduct){
            $allSizeProductArray[] = [
            // $oneSizeProduct->getTaille() => $oneSizeProduct->getStock()
            ];
        }
        return new JsonResponse($allSizeProductArray);
    }
}


<?php

namespace App\Services;

use Exception;
use App\Entity\ProduitBySize;
use App\Repository\ProduitBySizeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProduitBySizeService
{
    private ProduitBySizeRepository $produitBySizeRepository;

    /**
     * @param ProduitBySizeRepository $produitBySizeRepository
     */
    public function __construct(ProduitBySizeRepository $produitBySizeRepository)
    {
        $this->produitBySizeRepository = $produitBySizeRepository;
    }

    public function updateStockInCart($productId, $sizeId, $stock, $type): JsonResponse
    {
        $productBysize = $this->produitBySizeRepository->findByIdProductAndSize($productId, $sizeId)[0];

        if($type == 'increment') {
            $productBysize->setStock($productBysize->getStock() + $stock);
        } elseif($type == 'decrement') {
            $productBysize->setStock($productBysize->getStock() - $stock);
        } else {
            return new JsonResponse([
                "errorCode" => "040",
                "errorMessage" => "Le type doit être decrement ou increment"
            ], 404);
        }
        $this->produitBySizeRepository->save($productBysize, true);
        return new JsonResponse([
            "id" => $productBysize->getId(),
            "product" => $productBysize->getProduit(),
            "message" => "Le stock a bien été modifié"
        ], 200);
    }
}

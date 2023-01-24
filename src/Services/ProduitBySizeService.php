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

    public function updateStockInCart($data, $type): JsonResponse
    {
        foreach($data as $item) {
            $productBysize = $this->produitBySizeRepository->findByIdProductAndSize($item['productId'], $item['size'])[0];

            $ids = [];

            if($type == 'increment') {
                $productBysize->setStock($productBysize->getStock() + $item['stock']);
            } elseif($type == 'decrement') {
                $productBysize->setStock($productBysize->getStock() - $item['stock']);
            } else {
                return new JsonResponse([
                    "errorCode" => "040",
                    "errorMessage" => "Le type doit être decrement ou increment"
                ], 404);
            }
            $this->produitBySizeRepository->save($productBysize, true);
            $ids[] = $productBysize->getId();
        }
        return new JsonResponse([
            "id" => $ids,
            "message" => "Le stock a bien été modifié"
        ], 200);
    }
}

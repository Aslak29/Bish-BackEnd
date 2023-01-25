<?php

namespace App\Services;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProduitService
{
    private ProduitRepository $produitRepository;

    /**
     * @param ProduitRepository $produitRepository
     */
    public function __construct(ProduitRepository $produitRepository)
    {
        $this->produitRepository = $produitRepository;
    }

    public function update($name): JsonResponse
    {
        $produits = $this->produitRepository->likeName($name);
        $jsonProduit = null;
        foreach ($produits as $produit){
            $jsonProduit[] = [
                "id" => $produit->getId(),
                "name" => $produit->getName(),
                "price" => $produit->getPrice(),
                "pathImage" => $produit->getPathImage()
            ];
        }
        return new JsonResponse($jsonProduit);
    }


}
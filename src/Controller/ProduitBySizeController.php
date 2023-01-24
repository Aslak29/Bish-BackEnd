<?php

namespace App\Controller;

use App\Services\ProduitBySizeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;

#[Route('api/produit/by/size')]
class ProduitBySizeController extends AbstractController
{
    private ProduitBySizeService $produitBySizeService;

    /**
     * @param CodePromoService $codePromoService
     */
    public function __construct(ProduitBySizeService $produitBySizeService)
    {
        $this->produitBySizeService = $produitBySizeService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="ProduitBySize")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/updateStockInCart/{productId}/{sizeId}/{stock}/{type}', name: 'app_product_by_size_update_stock', methods: "POST")]
    public function updateStockInCart(Request $request): JsonResponse
    {
        return $this->produitBySizeService->updateStockInCart($request->attributes->get("productId"), $request->attributes->get("sizeId"), $request->attributes->get("stock"), $request->attributes->get("type"));
    }
}

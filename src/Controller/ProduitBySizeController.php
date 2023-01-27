<?php

namespace App\Controller;

use App\Services\ProduitBySizeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;

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
    #[Route('api/authenticated/produit/by/size/updateStockInCart/{type}', name: 'app_product_by_size_update_stock', methods: "POST")]
    public function updateStockInCart(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->produitBySizeService->updateStockInCart($data, $request->attributes->get("type"));
    }
}

<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\PromotionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Annotations as OA;


#[Route('/api/promotion')]
class PromotionController extends AbstractController
{
    /**
     * @param PromotionsRepository $promotionRepository
     * @return JsonResponse
     * @OA\Tag (name="Promotion")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/', name: 'app_promotion', methods: ['GET'])]
    public function index(PromotionsRepository $promotionRepository): JsonResponse
    {
        $promotions =  $promotionRepository->findAll();
        $promoArray = [];

        foreach ($promotions as $promotion) {
            $promoArray[] = [
                'id' => $promotion->getId(),
                'remise' => $promotion->getRemise()
            ];
        }
        return new JsonResponse($promoArray, 200);
    }

    /**
     * @param PromotionsRepository $promotionRepository
     * @return JsonResponse
     * @OA\Tag (name="Promotion")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/remove/{id}', name: 'app_promotion_remove', methods: ['DELETE'])]
    public function
    removePromotion(PromotionsRepository $promotionsRepository, ProduitRepository $produitRepository, Request $request)
    :JsonResponse
    {

        $promotion = $promotionsRepository->find($request->attributes->get('id'));
        $produitsByPromo = $produitRepository->findProductByIdPromo($request->attributes->get('id'));

        if ($promotion === null) {
            return new JsonResponse([
                "errorCode" => "A définir",
                "errorMessage" => "This promotions dont exist"
            ]);
        }else {
            foreach ($produitsByPromo as $produit) {
                $produit->setPromotions(null);
            }
            $promotionsRepository->remove($promotion, true);
            return new JsonResponse([
                "This promotion has been remove"
            ]);
        }
    }
}

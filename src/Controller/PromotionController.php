<?php

namespace App\Controller;

use App\Repository\PromotionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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

        foreach ($promotions as $promotion){
            $promoArray[] = [
                'id' => $promotion->getId(),
                'remise' => $promotion->getRemise()
            ];
        }
        return new JsonResponse($promoArray,200);
    }
}

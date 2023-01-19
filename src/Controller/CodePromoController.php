<?php

namespace App\Controller;

use App\Services\CodePromoService;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/code/promo')]
class CodePromoController extends AbstractController
{
    private CodePromoService $codePromoService;

    /**
     * @param CodePromoService $codePromoService
     */
    public function __construct(CodePromoService $codePromoService)
    {
        $this->codePromoService = $codePromoService;
    }

    /**
     * @return JsonResponse
     * @OA\Tag (name="CodePromo")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/findForAdmin', name: 'app_code_promo', methods: "GET")]
    public function readAll(): JsonResponse
    {
        return $this->codePromoService->findAll();
    }
}

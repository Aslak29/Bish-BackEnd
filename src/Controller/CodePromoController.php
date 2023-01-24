<?php

namespace App\Controller;

use App\Services\CodePromoService;
use Exception;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="CodePromo")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @throws Exception
     */
    #[Route('/create', name: 'app_create_code_promo', methods: "POST")]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->codePromoService->create($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="CodePromo")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @throws Exception
     */
    #[Route('/update', name: 'app_update_code_promo', methods: "PUT")]
    public function update(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->codePromoService->update($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="CodePromo")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @throws Exception
     */
    #[Route('/delete/{id}', name: 'app_delete_code_promo', methods: "DELETE")]
    public function delete(Request $request): JsonResponse
    {
        return $this->codePromoService->delete($request->attributes->get("id"));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="CodePromo")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/multipleRemove', name: 'app_multiple_delete_code_promo', methods: "DELETE")]
    public function multipleRemoveCodePromo(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->codePromoService->multipleRemoveCodePromo($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="CodePromo")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/findByName', name: 'app_find_by_name_code_promo', methods: "POST")]
    public function findByName(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->codePromoService->findByName($data['name']);
    }

}

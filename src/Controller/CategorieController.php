<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route('/api/categorie')]
class CategorieController extends AbstractController
{

    /**
     * @param CategorieRepository $categorieRepository
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/', name: 'app_categorie', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): JsonResponse
    {
        $categories =  $categorieRepository->findAll();
        $arrayCategories = [];

        foreach ($categories as $categorie){
            $arrayCategories[] = [
                'id' => $categorie->getId(),
                'name' => $categorie->getName(),
                'pathImage' => $categorie->getPathImage(),
                'isTrend' => $categorie->isIsTrend(),
                'pathImageTrend' => $categorie->getPathImageTrend()
            ];
        }
        return new JsonResponse($arrayCategories,200);
    }

    /**
     * @param CategorieRepository $categorieRepository
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/isTrend', name: 'categorie_is_trend', methods: ['GET'])]
    public function searchIsTrend(CategorieRepository $categorieRepository): JsonResponse
    {
        $categories =  $categorieRepository->getCategorieIsTrend();
        if (sizeof($categories) !== 0){
            $arrayCategories = [];
            foreach ($categories as $category){
                $arrayCategories[] = [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'pathImage' => $category->getPathImage(),
                    'isTrend' => $category->isIsTrend(),
                    'pathImageTrend' => $category->getPathImageTrend()
                ];
            }
            return new JsonResponse($arrayCategories,200);
        }else{
            return new JsonResponse([
                "errorCode" => "A définir",
                "errorMessage" => "Aucune catégorie est en tendance"
            ],409);
        }

    }


}

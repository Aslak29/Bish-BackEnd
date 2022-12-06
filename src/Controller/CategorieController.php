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
        shuffle($categories);
        $arrayCategories = [];

        for($i=0; $i<1; $i++){
            $arrayCategories[] = [
                'id' => $categories[$i]->getId(),
                'name' => $categories[$i]->getName(),
                'pathImage' => $categories[$i]->getPathImage(),
                'isTrend' => $categories[$i]->isIsTrend(),
                'pathImageTrend' => $categories[$i]->getPathImageTrend()
            ];
        }
        return new JsonResponse($arrayCategories,200);
    }


}

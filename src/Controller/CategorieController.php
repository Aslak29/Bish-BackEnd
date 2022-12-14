<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $categories = $categorieRepository->findAll();
        $arrayCategories = [];

        foreach ($categories as $categorie) {
            $arrayCategories[] = [
                'id' => $categorie->getId(),
                'name' => $categorie->getName(),
                'pathImage' => $categorie->getPathImage(),
                'isTrend' => $categorie->isIsTrend(),
                'pathImageTrend' => $categorie->getPathImageTrend()
            ];
        }
        return new JsonResponse($arrayCategories, 200);
    }

    /**
     * @param CategorieRepository $categorieRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/create/{name}/{trend}/{pathImage}', name: 'app_create_categorie', methods: ['POST'])]
    public function create(CategorieRepository $categorieRepository, Request $request): JsonResponse
    {
        $categorie = new Categorie();

        $categorie->setName($request->attributes->get('name'));

        if ($request->attributes->get('trend') === "true") {
            $categorie->setIsTrend(true);
        } elseif ($request->attributes->get('trend') === "false") {
            $categorie->setIsTrend(false);
        } else {
            return new JsonResponse([
                "errorCode" => "004",
                "errorMessage" => "is_trend is not boolean !"
            ], 406);
        }

        $categorie->setPathImage($request->attributes->get('pathImage'));

        $categorieRepository->save($categorie, true);


        $categorieArray = [
            "id" => $categorie->getId(),
            "name" => $categorie->getName()
        ];

        return new JsonResponse($categorieArray, 200);
    }

    /**
     * @param CategorieRepository $categorieRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/update/{id}/{name}/{trend}/{pathImage}', name: 'app_update_categorie', methods: ['POST'])]
    public function update(CategorieRepository $categorieRepository, Request $request): JsonResponse
    {
        $categorie = $categorieRepository->find($request->attributes->get('id'));

        $categorie->setName($request->attributes->get('name'));

        if ($request->attributes->get('trend') === "true") {
            $categorie->setIsTrend(true);
        } elseif ($request->attributes->get('trend') === "false") {
            $categorie->setIsTrend(false);
        } else {
            return new JsonResponse([
                "errorCode" => "004",
                "errorMessage" => "is_trend is not boolean !"
            ], 406);
        }

        $categorie->setPathImage($request->attributes->get('pathImage'));

        $categorieRepository->save($categorie, true);

        $categorieArray = [
            "id" => $categorie->getId(),
            "name" => $categorie->getName()
        ];

        return new JsonResponse($categorieArray, 200);
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
        $categories = $categorieRepository->getCategorieIsTrend();
        if (sizeof($categories) !== 0) {
            $arrayCategories = [];
            foreach ($categories as $category) {
                $arrayCategories[] = [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'pathImage' => $category->getPathImage(),
                    'isTrend' => $category->isIsTrend(),
                    'pathImageTrend' => $category->getPathImageTrend()
                ];
            }
            return new JsonResponse($arrayCategories, 200);
        } else {
            return new JsonResponse([
                "errorCode" => "A définir",
                "errorMessage" => "Aucune catégorie est en tendance"
            ], 409);
        }

    }


}

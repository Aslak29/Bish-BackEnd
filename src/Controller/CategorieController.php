<?php
namespace App\Controller;

use App\Entity\Categorie;
use App\GlobalFunction;
use App\GlobalFunction\FunctionErrors;
use App\Repository\CategorieRepository;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

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
    #[Route('api/public/categorie/', name: 'app_categorie', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): JsonResponse
    {
        $categories = $categorieRepository->getCategorieAvailable();
        $arrayCategories = [];

        $i = 0;
        foreach ($categories as $categorie) {
            $arrayCategories[] = [
                'id' => $categorie->getId(),
                'name' => $categorie->getName(),
                'pathImage' => $categorie->getPathImage(),
                'isTrend' => $categorie->isIsTrend(),
                'pathImageTrend' => $categorie->getPathImageTrend(),
                'countProduitAvailable' => null
            ];

            $countProduit = [];
            foreach ($categorie->getProduits() as $produit) {
                if ($produit->isIsAvailable()) {
                    $countProduit[] = $produit;
                }
            }

            $arrayCategories[$i]['countProduit'] = count($countProduit);

            $i++;
        }
        return new JsonResponse($arrayCategories, 200);
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
    #[Route('api/admin/categorie/forAdmin', name: 'app_categorie_admin', methods: ['GET'])]
    public function showAdmin(CategorieRepository $categorieRepository): JsonResponse
    {
        $categories = $categorieRepository->findAll();
        $arrayCategories = [];

        foreach ($categories as $categorie) {
            $arrayCategories[] = [
                'id' => $categorie->getId(),
                'name' => $categorie->getName(),
                'pathImage' => $categorie->getPathImage(),
                'isTrend' => $categorie->isIsTrend(),
                'available' => $categorie->isAvailable(),
                'pathImageTrend' => $categorie->getPathImageTrend()
            ];
        }
        return new JsonResponse($arrayCategories, 200);
    }

    /**
     * @param CategorieRepository $categorieRepository
     * @param Request $request
     * @param FunctionErrors $errorCode
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('api/admin/categorie/create/{name}/{trend}/{available}/{pathImage}', name: 'app_create_categorie', methods: ['POST'])]
    public function create(
        CategorieRepository $categorieRepository, Request $request, GlobalFunction\FunctionErrors $errorCode
    ): JsonResponse
    {
        $categorie = new Categorie();

        $categorie->setName($request->attributes->get('name'));

        if ($request->attributes->get('trend') === "true") {
            $categorie->setIsTrend(true);
        } elseif ($request->attributes->get('trend') === "false") {
            $categorie->setIsTrend(false);
        } else {
            return $errorCode->generateCodeError004();
        }

        if ($request->attributes->get('available') === "true") {
            $categorie->setAvailable(true);
        }elseif ($request->attributes->get('available') === "false") {
            $categorie->setAvailable(false);
        }else {
            return $errorCode->generateCodeError005();
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
     * @param FunctionErrors $errorCode
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('api/admin/categorie/update/{id}/{name}/{trend}/{available}/{pathImage}', name: 'app_update_categorie', methods: ['PUT'])]
    public function update(
        CategorieRepository $categorieRepository, Request $request, GlobalFunction\FunctionErrors $errorCode
    ): JsonResponse
    {
        $categorie = $categorieRepository->find($request->attributes->get('id'));

        $categorie->setName($request->attributes->get('name'));

        if ($request->attributes->get('trend') === "true") {
            $categorie->setIsTrend(true);
        } elseif ($request->attributes->get('trend') === "false") {
            $categorie->setIsTrend(false);
        } else {
            return $errorCode->generateCodeError004();
        }

        if ($request->attributes->get('available') === "true") {
            $categorie->setAvailable(true);
        }elseif ($request->attributes->get('available') === "false") {
            $categorie->setAvailable(false);
        }else {
            return $errorCode->generateCodeError005();
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
     * @param FunctionErrors $errorCode
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @author
     */
    #[Route('api/admin/categorie/delete/{id}', name: 'app_delete_categorie', methods: ['DELETE'])]
    public function removeCategory(
        CategorieRepository $categorieRepository, GlobalFunction\FunctionErrors $errorCode, Request $request
    ): JsonResponse
    {
        $categorie = $categorieRepository->find($request->attributes->get('id'));
        $categorieRepository->remove($categorie, true);

        return new JsonResponse(null, 200);
    }

    /**
     * @param CategorieRepository $categorieRepository
     * @param Request $request
     * @param FunctionErrors $errorCode
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('api/admin/categorie/updateIsTrend/{id}/{isTrend}/',
        name: 'app_update_category_trend', methods: "PUT")]
    public function updateTrendCategory(
        CategorieRepository $categorieRepository, Request $request, FunctionErrors $errorCode
    ): JsonResponse
    {
        $categorie = $categorieRepository->find($request->attributes->get('id'));

        if (!$categorie) {
            return $errorCode->generateCodeError003();
        }

        if ($request->attributes->get('isTrend') === "true") {
            $categorie->setIsTrend(true);
        }elseif ($request->attributes->get('isTrend') === "false") {
            $categorie->setIsTrend(false);
        }else {
            return $errorCode->generateCodeError004();
        }

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
     * @param FunctionErrors $errorCode
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('api/admin/blog/updateAvailable/{id}/{available}/',
        name: 'app_update_category_available', methods: "PUT")]
    public function updateAvailableCategory(
        CategorieRepository $categorieRepository, Request $request, FunctionErrors $errorCode
    ): JsonResponse
    {
        $categorie = $categorieRepository->find($request->attributes->get('id'));

        if (!$categorie) {
            return $errorCode->generateCodeError003();
        }

        if ($request->attributes->get('available') === "true") {
            $categorie->setAvailable(true);
        }elseif ($request->attributes->get('available') === "false") {
            $categorie->setAvailable(false);
        }else {
            return $errorCode->generateCodeError005();
        }

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
    #[Route('api/public/categorie/isTrend', name: 'categorie_is_trend', methods: ['GET'])]
    public function searchIsTrend(CategorieRepository $categorieRepository): JsonResponse
    {
        $categories = $categorieRepository->getCategorieIsTrend();

        if(count($categories) > 0) {
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
        } else {
            return new JsonResponse([null], 200);
        }
    }

    /**
     * @param CategorieRepository $categorieRepository
     * @param Request $request
     * @param FunctionErrors $errorCode
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('api/admin/categorie/multipleUpdateIsTrend/{isTrend}/',
        name: 'app_multiple_update_category_trend', methods: "PUT")]
    public function multipleUpdateTrendCategory(
        CategorieRepository $categorieRepository, Request $request, FunctionErrors $errorCode
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach($data as $id) {
            $categorie = $categorieRepository->find($id);

            if (!$categorie) {
                return $errorCode->generateCodeError003();
            }

            if ($request->attributes->get('isTrend') === "true") {
                $categorie->setIsTrend(true);
            }elseif ($request->attributes->get('isTrend') === "false") {
                $categorie->setIsTrend(false);
            }else {
                return $errorCode->generateCodeError004();
            }

            $categorieRepository->save($categorie, true);
        }
        
        return new JsonResponse(null, 200);
    }

    /**
     * @param CategorieRepository $categorieRepository
     * @param Request $request
     * @param FunctionErrors $errorCode
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('api/admin/categorie/multipleUpdateAvailable/{available}/',
        name: 'app_multiple_update_category_available', methods: "PUT")]
    public function multipleUpdateAvailableCategory(
        CategorieRepository $categorieRepository, Request $request, FunctionErrors $errorCode
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach($data as $id) {
            $categorie = $categorieRepository->find($id);

            if (!$categorie) {
                return $errorCode->generateCodeError003();
            }

            if ($request->attributes->get('available') === "true") {
                $categorie->setAvailable(true);
            }elseif ($request->attributes->get('available') === "false") {
                $categorie->setAvailable(false);
            }else {
                return $errorCode->generateCodeError005();
            }

            $categorieRepository->save($categorie, true);
        }
        
        return new JsonResponse(null, 200);
    }

    /**
     * @param CategorieRepository $categorieRepository
     * @param FunctionErrors $errorCode
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Categorie")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @author
     */
    #[Route('api/public/categorie/multipleDelete', name: 'app_multiple_delete_categorie', methods: ['PUT'])]
    public function multipleRemoveCategory(
        CategorieRepository $categorieRepository, GlobalFunction\FunctionErrors $errorCode, Request $request
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach($data as $id) {
            $categorie = $categorieRepository->find($id);
            $categorieRepository->remove($categorie, true);
        }
        
        return new JsonResponse(null, 200);
    }
}

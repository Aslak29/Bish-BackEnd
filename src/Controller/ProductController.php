<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit;


// exporter vers AdminProductView ? - Flo
#[Route('api/produit')]
class ProductController extends AbstractController
{
        /**
     * @param ProduitRepository $produitRepository
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */

    #[Route('/', name: 'app_produit', methods:"GET")]
    public function findProduct(ProduitRepository $produitRepository): JsonResponse
    {
        $produits = $produitRepository->findAll();
        $produitArray = [];
        foreach($produits as $produit){
            $produitArray[] = [
                'id' => $produit->getId(),
                'name' => $produit->getName(),
                'description' => $produit->getDescription(),
                'pathImage' => $produit->getPathImage(),
                'price' => $produit->getPrice(),
                'is_trend' => $produit->isIsTrend(),
                'is_available' => $produit->isIsAvailable(),
                'id_categorie' => $produit->getCategories()[0]->getId()
            ];
        }
        return new JsonResponse($produitArray);
    }

    /**
     * @param ProduitRepository $produitRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/find/{id}', name: 'app_produit_by_id', methods:"POST")]
    public function findProductById(ProduitRepository $produitRepository,Request $request): JsonResponse
    {
        $produit = $produitRepository->findOneById($request->attributes->get('id'));
        if (!$produit){
            return new JsonResponse([
                "errorCode" => "002",
                "errorMessage" => "le produit n'existe pas !"
            ],404);
        }else{
            $produit = $produit[0];
        }
        $produitArray[] = [
            'id' => $produit->getId(),
            'name' => $produit->getName(),
            'description' => $produit->getDescription(),
            'pathImage' => $produit->getPathImage(),
            'price' => $produit->getPrice(),
            'is_trend' => $produit->isIsTrend(),
            'is_available' => $produit->isIsAvailable(),
            'id_categorie' => $produit->getCategories()[0] === null ? "-" : $produit->getCategories()[0]->getId()
        ];

        return new JsonResponse($produitArray);
    }

    /**
     * @param ProduitRepository $produitRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/add/{name}/{description}/{pathImage}/{price}/{is_trend}/{is_available}', name: 'app_add_product', methods: "POST")]
    public function addProduit(ProduitRepository $produitRepository, Request $request):JsonResponse
    {
        $produit = new Produit();
        $produit->setName($request->attributes->get('name'));
        $produit->setDescription($request->attributes->get('description'));
        $produit->setPathImage($request->attributes->get('pathImage'));
        $produit->setPrice(floatval( $request->attributes->get('price'))); 
        $produit->setIsTrend($request->attributes->get('is_trend'));
        $produit->setIsAvailable($request->attributes->get('is_available'));

        $produitRepository->save($produit,true);

        return new JsonResponse(null,200);

    }

     /**
     * @param ProduitRepository $produitRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */

    #[Route('/filter/{orderby}/{moyenne}/{minprice}/{maxprice}', name: 'app_filter_product', methods: "POST")]
    public function searchFilter(ProduitRepository $produitRepository,Request $request):JsonResponse
    {
        $produits = $produitRepository->findByFilter($request->attributes->get("orderby"),$request->attributes->get("moyenne"),$request->attributes->get("minprice"),$request->attributes->get("maxprice"));
        $produitArray = [];
        foreach($produits as $produit){
            $produitArray[] = [
                'id' => $produit->getId(),
                'name' => $produit->getName(),
                'description' => $produit->getDescription(),
                'pathImage' => $produit->getPathImage(),
                'price' => $produit->getPrice(),
                'is_trend' => $produit->isIsTrend(),
                'is_available' => $produit->isIsAvailable()
            ];
        }
        return new JsonResponse($produitArray);
    }


    /**
     * @param ProduitRepository $produitRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/suggestions/{idCategorie}/{id}', name: 'product_suggest', methods: "POST")]
    public function findProductsByCat(ProduitRepository $produitRepository, Request $request): JsonResponse
    {
        $produits = $produitRepository->findAllProductsByIdCateg($request->attributes->get('idCategorie'), $request->attributes->get('id'));
        if (!$produits) {
            return new JsonResponse([
                "errorCode" => "003",
                "errorMessage" => "La catégorie n'existe pas"
            ], 404);
        }
        shuffle($produits);
        $produitArray = [];
        for($i=0; $i<4; $i++){
            $produitArray[] = [
                'id' => $produits[$i]->getId(),
                'name' => $produits[$i]->getName(),
                'description' => $produits[$i]->getDescription(),
                'pathImage' => $produits[$i]->getPathImage(),
                'price' => $produits[$i]->getPrice(),
                'is_trend' => $produits[$i]->isIsTrend(),
                'is_available' => $produits[$i]->isIsAvailable()
            ];
        }
        return new JsonResponse($produitArray);
    }

    /**
     * @param ProduitRepository $produitRepository
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/isTrend', name: 'produit_is_trend', methods: ['POST'])]
    public function searchProduitIsTrend(ProduitRepository $produitRepository): JsonResponse
    {
        $produits =  $produitRepository->getProduitIsTrend();
        shuffle($produits);
        $arrayProduits = [];

        for($i=0; $i<2; $i++){
            $arrayProduits[] = [
                'id' => $produits[$i]->getId(),
                'name' => $produits[$i]->getName(),
                'description' => $produits[$i]->getDescription(),
                'pathImage' => $produits[$i]->getPathImage(),
                'price' => $produits[$i]->getPrice(),
                'is_trend' => $produits[$i]->isIsTrend(),
                'is_available' => $produits[$i]->isIsAvailable()
            ];
        }
        return new JsonResponse($arrayProduits,200);
    }



    /**
     * @param ProduitRepository $produitRepository
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/bestPromo', name: 'best_promo', methods: ['GET'])]
    public function findBestPromo(ProduitRepository $produitRepository): JsonResponse
    {
        $produit =  $produitRepository->findByBestPromo();
        $arrayProduits[] = [
            "id" => $produit[0]->getId(),
            "name"=>$produit[0]->getName(),
            "price"=>$produit[0]->getPrice(),
            "description"=>$produit[0]->getDescription(),
            "path_image"=>$produit[0]->getPathImage(),
            "created-at"=>$produit[0]->getCreatedAt(),
            "is_trend"=>$produit[0]->isIsTrend(),
            "is_available"=>$produit[0]->isIsAvailable(),
        ];
        return new JsonResponse($arrayProduits,200);
    }
}

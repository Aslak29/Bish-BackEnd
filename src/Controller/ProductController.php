<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Produit;
use function PHPUnit\Framework\isEmpty;

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
     * @OA\Tag (name="ProduitById")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/{id}', name: 'app_produit_by_id', methods:"GET")]
    public function findProductById(ProduitRepository $produitRepository,Request $request): JsonResponse
    {
        $produit = $produitRepository->findOneById($request->attributes->get('id'));
        if (!$produit){
            return new JsonResponse([
                "errorCode" => "002",
                "errorMessage" => "le produit n'éxiste pas !"
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
    public function addProduit(ProduitRepository $produitRepository, Request $request){
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
    #[Route('/suggestions/{idCategorie}', name: 'product_suggest', methods: "POST")]
    public function findProductsByCat(ProduitRepository $produitRepository, Request $request): JsonResponse
    {
        $produits = $produitRepository->findAllProductsByIdCateg($request->attributes->get('idCategorie'));
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
}

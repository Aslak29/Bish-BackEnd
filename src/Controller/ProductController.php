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
    #[Route('/find/{id}', name: 'app_produit_by_id', methods:"POST")]
    public function findProductById(ProduitRepository $produitRepository,Request $request): JsonResponse
    {
        $produit = $produitRepository->findOneBy(array('id' => $request->attributes->get('id')));
        if (!$produit){
            return new JsonResponse([
                "errorCode" => "002",
                "errorMessage" => "le produit n'existe pas !"
            ],404);
        }
        $produitArray[] = [
            'id' => $produit->getId(),
            'name' => $produit->getName(),
            'description' => $produit->getDescription(),
            'pathImage' => $produit->getPathImage(),
            'price' => $produit->getPrice(),
            'is_trend' => $produit->isIsTrend(),
            'is_available' => $produit->isIsAvailable()
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
    public function addProduit(ProduitRepository $produitRepository, Request $request): JsonResponse
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
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/isTrend', name: 'produit_is_trend', methods: ['GET'])]
    public function searchProduitIsTrend(ProduitRepository $produitRepository): JsonResponse
    {
        $produits =  $produitRepository->getProduitIsTrend();
        $arrayProduits = [];

        foreach ($produits as $produit){
            $arrayProduits[] = [
                'id' => $produit->getId(),
                'name' => $produit->getName(),
                'description' => $produit->getDescription(),
                'pathImage' => $produit->getPathImage(),
                'price' => $produit->getPrice(),
                'is_trend' => $produit->isIsTrend(),
                'is_available' => $produit->isIsAvailable()
            ];
        }
        return new JsonResponse($arrayProduits,200);
    }
}

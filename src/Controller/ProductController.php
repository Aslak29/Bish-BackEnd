<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Repository\TailleRepository;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit;
use App\Entity\ProduitBySize;
use App\Repository\NoteRepository;
use App\Repository\ProduitBySizeRepository;

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
            $jsonProduct = [
                'id' => $produit->getId(),
                'name' => $produit->getName(),
                'description' => $produit->getDescription(),
                'pathImage' => $produit->getPathImage(),
                'price' => $produit->getPrice(),
                'created_at' => $produit->getCreatedAt(),
                'is_trend' => $produit->isIsTrend(),
                'is_available' => $produit->isIsAvailable(),
                "stockBySize" => array(),
                'id_categorie' => $produit->getCategories()[0] === null ? "-" : $produit->getCategories()[0]->getId(),
                'name_categorie' => $produit->getCategories()[0] === null ? "-" : $produit->getCategories()[0]->getName(),
                'promotion' =>
                $produit->getPromotions() !== null ? [
                    'id' => $produit->getPromotions()->getId(),
                    'remise' => $produit->getPromotions()->getRemise(),
                    'price_remise' => round($produit->getPrice() - (($produit->getPrice() * $produit->getPromotions()->getRemise())/ 100), 2),
                    'date_start' => $produit->getPromotions()->getDateStart()->format("d-m-Y"),
                    'heure_start' => $produit->getPromotions()->getDateStart()->format("H:i:s"),
                    'date_end' => $produit->getPromotions()->getDateEnd()->format("d-m-Y"),
                    'heure_end' => $produit->getPromotions()->getDateEnd()->format("H:i:s"),
                ] : [],
            ];
            foreach ($produit->getProduitBySize() as $size){
                $jsonProduct['stockBySize'][] = [
                    "taille" =>$size->getTaille()->getTaille(),
                    "stock" =>$size->getStock()
                ];
            }
            $produitArray[] = $jsonProduct;
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
                "errorMessage" => "le produit n'éxiste pas !"
            ],404);
        }else{
            $produit = $produit[0];
        }
        $produitArray = [
            'id' => $produit->getId(),
            'name' => $produit->getName(),
            'description' => $produit->getDescription(),
            'pathImage' => $produit->getPathImage(),
            'price' => $produit->getPrice(),
            'is_trend' => $produit->isIsTrend(),
            'is_available' => $produit->isIsAvailable(),
            "stockBySize" => array(),
            'id_categorie' => $produit->getCategories()[0] === null ? "-" : $produit->getCategories()[0]->getId(),
            'promotion' =>
                $produit->getPromotions() !== null ? [
                    'id' => $produit->getPromotions()->getId(),
                    'remise' => $produit->getPromotions()->getRemise(),
                    'price_remise' => round($produit->getPrice() - (($produit->getPrice() * $produit->getPromotions()->getRemise())/ 100), 2),
                    'date_start' => $produit->getPromotions()->getDateStart()->format("d-m-Y"),
                    'heure_start' => $produit->getPromotions()->getDateStart()->format("H:i:s"),
                    'date_end' => $produit->getPromotions()->getDateEnd()->format("d-m-Y"),
                    'heure_end' => $produit->getPromotions()->getDateEnd()->format("H:i:s"),
                ] : [],     
        ];
        foreach ($produit->getProduitBySize() as $size){
            $produitArray['stockBySize'][] = [
                "taille" =>$size->getTaille()->getTaille(),
                "stock" =>$size->getStock()
            ];
        }

        return new JsonResponse($produitArray);
    }

    /**
     * @param ProduitRepository $produitRepository
     * @param ProduitBySizeRepository $produitBySizeRepo
     * @param TailleRepository $tailleRepository
     * @param CategorieRepository $categorieRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/add/{name}/{description}/{pathImage}/{idCategorie}/{price}/{is_trend}/{is_available}/{xs}/{s}/{m}/{l}/{xl}/',
        name: 'app_add_product', methods: "POST")]
    public function addProduit(
        ProduitRepository $produitRepository,ProduitBySizeRepository $produitBySizeRepo,
        TailleRepository $tailleRepository,CategorieRepository $categorieRepository, Request $request
    ):JsonResponse
    {
        $produit = new Produit();
        /* Récupération de toutes les tailles en bdd */
        $tailles = $tailleRepository->findAll();
        /* Récupération de la catégorie demandée par rapport à son id en bdd */
        $categorie = $categorieRepository->findOneById($request->attributes->get('idCategorie'));
        /* Donnation des valeurs aux attributs du produit */
        $produit->setName($request->attributes->get('name'));
        $produit->setDescription($request->attributes->get('description'));
        $produit->setPathImage($request->attributes->get('pathImage'));
        $produit->setPrice(floatval( $request->attributes->get('price')));

        /* Vérifier si is_trend est bien un boolean */
        if ($request->attributes->get('is_trend') === "true"){
            $produit->setIsTrend(true);
        }elseif ($request->attributes->get('is_trend') === "false"){
            $produit->setIsTrend(false);
        }else {
            return new JsonResponse([
                "errorCode" => "004",
                "errorMessage" => "is_trend is not boolean !"
            ],406);
        }
        /* Vérifier si is_available est bien un boolean */
        if ($request->attributes->get('is_available') === "true"){
            $produit->setIsAvailable(true);
        }elseif ($request->attributes->get('is_available') === "false"){
            $produit->setIsAvailable(false);
        }else {
            return new JsonResponse([
                "errorCode" => "005",
                "errorMessage" => "is_available is not boolean !"
            ],406);
        }
        /* Vérifier si la catégorie existe bien en bdd pour faire la relation */
        if (!$categorie){
            return new JsonResponse([
                "errorCode" => "003",
                "errorMessage" => "la catégorie n'éxiste pas !"
            ],404);
        }else {
            $produit->addCategory($categorie[0]);
        }
        /* Première insertion en bdd pour le produit */
        $produitRepository->save($produit,true);

        foreach ($tailles as $taille){
            $produitBySize = new ProduitBySize();
            $produitBySize->setTaille($taille);
            $produitBySize->setStock(floatval($request->attributes->get($taille->getTaille())));
            $produitBySize->setProduit($produit);
            $produit->addProduitBySize($produitBySize);
            $produitBySizeRepo->save($produitBySize, true);
        }
        /* Deuxième insertion en bdd pour effectuer la relation des tailles pour le produit crée */
        $produitRepository->save($produit,true);

        $produitArray = [
            "id" => $produit->getId(),
            "name" => $produit->getName()
        ];

        return new JsonResponse($produitArray);
    }


    /**
     * @param ProduitRepository $produitRepository
     * @param Request $request
     * @param ProduitBySizeRepository $produitBySizeRepository
     * @param NoteRepository $noteRepository
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/remove/{id}', name: 'app_delete_product', methods: "DELETE")]
    public function removeProduit(ProduitRepository $produitRepository, Request $request, ProduitBySizeRepository $produitBySizeRepository, NoteRepository $noteRepository):JsonResponse
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

        if ($produit ->getProduitInCommande()[0] === null){
        foreach ($produit->getProduitBySize() as $size ){
            $produitBySizeRepository-> remove($size, true);
        }
        foreach ($produit->getNote() as $note){
            $noteRepository-> remove($note, true);
        }
        $produitRepository->remove($produit, true);
        }else{
            return new JsonResponse([
                "errorCode" => "006",
                "errorMessage" => "le produit est en commande"
            ],404);
        }

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

    #[Route('/filter/{orderby}/{moyenne}/{minprice}/{maxprice}/{idCategorie}/{limit}/{offset}', name: 'app_filter_product', methods: "POST")]
    public function searchFilter(ProduitRepository $produitRepository,Request $request):JsonResponse
    {
        $produits = $produitRepository->findByFilter($request->attributes->get("orderby"),$request->attributes->get("moyenne"),$request->attributes->get("minprice"),$request->attributes->get("maxprice"),$request->attributes->get("idCategorie"),$request->attributes->get('limit'),$request->attributes->get("offset"));
        $countProduits = $produitRepository->countByFilter($request->attributes->get("orderby"),$request->attributes->get("moyenne"),$request->attributes->get("minprice"),$request->attributes->get("maxprice"),$request->attributes->get("idCategorie"));
        $produitArray = [];
        foreach($produits as $produit){
            $jsonProduct = [
                'id' => $produit->getId(),
                'name' => $produit->getName(),
                'description' => $produit->getDescription(),
                'pathImage' => $produit->getPathImage(),
                'price' => round($produit->getPrice(), 2),
                'is_trend' => $produit->isIsTrend(),
                'is_available' => $produit->isIsAvailable(),
                "stockBySize" => array(),
                'id_categorie' => $produit->getCategories()[0] === null ? "-" : $produit->getCategories()[0]->getId(),
                'promotion' =>
                    $produit->getPromotions() !== null ? [
                        'id' => $produit->getPromotions()->getId(),
                        'remise' => $produit->getPromotions()->getRemise(),
                        'price_remise' => round($produit->getPrice() - (($produit->getPrice() * $produit->getPromotions()->getRemise())/ 100), 2),
                        'date_start' => $produit->getPromotions()->getDateStart()->format("d-m-Y"),
                        'heure_start' => $produit->getPromotions()->getDateStart()->format("H:i:s"),
                        'date_end' => $produit->getPromotions()->getDateEnd()->format("d-m-Y"),
                        'heure_end' => $produit->getPromotions()->getDateEnd()->format("H:i:s"),
                    ] : [],
            ];
            foreach ($produit->getProduitBySize() as $size){
                $jsonProduct['stockBySize'][] = [
                    "taille" =>$size->getTaille()->getTaille(),
                    "stock" =>$size->getStock()
                ];
            }
            $produitArray[] = $jsonProduct;
        }

        $resultArray = [];
        $resultArray[] = $produitArray;
        $resultArray[] = [
            "count" => $countProduits
        ];
        return new JsonResponse($resultArray);
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
        $product = $produitRepository->findAllProductsByIdCateg($request->attributes->get('idCategorie'), $request->attributes->get('id'));
        if (!$product) {
            return new JsonResponse([
                "errorCode" => "003",
                "errorMessage" => "La catégorie n'existe pas"
            ], 404);
        }
        shuffle($product);
        $produitSuggestion = array_slice($product, 0, 4);

        $produitArray = [];
        foreach ($produitSuggestion as $product){
            $jsonProduct = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'pathImage' => $product->getPathImage(),
                'price' => round($product->getPrice(), 2),
                'is_trend' => $product->isIsTrend(),
                'is_available' => $product->isIsAvailable(),
                'stockBySize' => array(),
                'id_categorie' => $product->getCategories()[0] === null ? "-" : $product->getCategories()[0]->getId(),
                'promotion' =>
                    $product->getPromotions() !== null ? [
                        'id' => $product->getPromotions()->getId(),
                        'remise' => $product->getPromotions()->getRemise(),
                        'price_remise' => round($product->getPrice() - (($product->getPrice() * $product->getPromotions()->getRemise())/ 100), 2),
                        'date_start' => $product->getPromotions()->getDateStart()->format("d-m-Y"),
                        'heure_start' => $product->getPromotions()->getDateStart()->format("H:i:s"),
                        'date_end' => $product->getPromotions()->getDateEnd()->format("d-m-Y"),
                        'heure_end' => $product->getPromotions()->getDateEnd()->format("H:i:s"),
                    ] : [],
            ];
            foreach ($product->getProduitBySize() as $size){
                $jsonProduct['stockBySize'][] = [
                    "taille" =>$size->getTaille()->getTaille(),
                    "stock" =>$size->getStock()
                ];
            }
            $produitArray[] = $jsonProduct;
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
    #[Route('/promotions', name: 'app_produit_promotion', methods:"GET")]
    public function PromoProduct(ProduitRepository $produitRepository): JsonResponse
    {
        $produits = $produitRepository->findProductPromo();
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
                'is_available' => $produits[$i]->isIsAvailable(),
                'id_categorie' => $produits[$i]->getCategories()[0] === null ? "-" : $produits[$i]->getCategories()[0]->getId(),
                'promotion' =>
                    $produits[$i]->getPromotions() !== null ? [
                        'id' => $produits[$i]->getPromotions()->getId(),
                        'remise' => $produits[$i]->getPromotions()->getRemise(),
                        'price_remise' => round($produits[$i]->getPrice() - (($produits[$i]->getPrice() * $produits[$i]->getPromotions()->getRemise())/ 100), 2),
                        'date_start' => $produits[$i]->getPromotions()->getDateStart()->format("d-m-Y"),
                        'heure_start' => $produits[$i]->getPromotions()->getDateStart()->format("H:i:s"),
                        'date_end' => $produits[$i]->getPromotions()->getDateEnd()->format("d-m-Y"),
                        'heure_end' => $produits[$i]->getPromotions()->getDateEnd()->format("H:i:s"),
                        ] : [],
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
    #[Route('/count', name: 'product_count', methods: "GET")]
    public function countProduct(ProduitRepository $produitRepository):JsonResponse{

        $countProduit = $produitRepository->countProduit();
        return new JsonResponse($countProduit[0]);

    }
}

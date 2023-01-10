<?php

namespace App\Controller;

use App\GlobalFunction\FunctionErrors;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Repository\PromotionsRepository;
use App\Repository\TailleRepository;
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
        foreach ($produits as $produit) {
            $jsonProduct = [
                'id' => $produit->getId(),
                'name' => $produit->getName(),
                'description' => $produit->getDescription(),
                'pathImage' => $produit->getPathImage(),
                'price' => $produit->getPrice(),
                'created_at' => $produit->getCreatedAt(),
                'is_trend' => $produit->isIsTrend(),
                'is_available' => $produit->isIsAvailable(),
                "stockBySize" => null,
                'id_categorie' =>
                    $produit->getCategories()[0] === null ? "-" : $produit->getCategories()[0]->getId(),
                'name_categorie' =>
                    $produit->getCategories()[0] === null ? "-" : $produit->getCategories()[0]->getName(),
                'noteAverage' => null,
                'inCommande' => $produit->getProduitInCommande()[0] === null ? false : true,
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
            foreach ($produit->getProduitBySize() as $size) {
                $jsonProduct['stockBySize'][$size->getTaille()->getTaille()] = $size->getStock();
            }
            $nbNote = 0;
            $totalNote = 0;
            foreach ($produit->getNote() as $note) {
                $nbNote++;
                $totalNote += $note->getNote();
            }
            $nbNote > 0 && $jsonProduct['noteAverage'] = $totalNote / $nbNote;
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
    public function findProductById(ProduitRepository $produitRepository, Request $request): JsonResponse
    {
        $produit = $produitRepository->findOneById($request->attributes->get('id'));
        if (!$produit) {
            return new JsonResponse([
                "errorCode" => "002",
                "errorMessage" => "le produit n'existe pas !"
            ], 404);
        }else {
            $produit = $produit[0];
        }
        $produitArray = [
            'id' => $produit[0]->getId(),
            'name' => $produit[0]->getName(),
            'description' => $produit[0]->getDescription(),
            'pathImage' => $produit[0]->getPathImage(),
            'price' => $produit[0]->getPrice(),
            'is_trend' => $produit[0]->isIsTrend(),
            'is_available' => $produit[0]->isIsAvailable(),
            "stockBySize" => array(),
            'noteAverage' => $produit[1] !== null ? round($produit[1],1) : $produit[1],
            'id_categorie' => $produit[0]->getCategories()[0] === null ? "-" : $produit[0]->getCategories()[0]->getId(),
            'promotion' =>
                $produit[0]->getPromotions() !== null ? [
                    'id' => $produit[0]->getPromotions()->getId(),
                    'remise' => $produit[0]->getPromotions()->getRemise(),
                    'price_remise' => round($produit[0]->getPrice() - (($produit[0]->getPrice() * $produit[0]->getPromotions()->getRemise())/ 100), 2),
                    'date_start' => $produit[0]->getPromotions()->getDateStart()->format("d-m-Y"),
                    'heure_start' => $produit[0]->getPromotions()->getDateStart()->format("H:i:s"),
                    'date_end' => $produit[0]->getPromotions()->getDateEnd()->format("d-m-Y"),
                    'heure_end' => $produit[0]->getPromotions()->getDateEnd()->format("H:i:s"),
                ] : [],
                ];
        foreach ($produit[0]->getProduitBySize() as $size) {
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
     * @param PromotionsRepository $promotionsRepository
     * @param Request $request
     * @param FunctionErrors $errorsCode
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     *
     */
    #[Route('/add', name: 'app_add_product', methods: "POST")]
    public function addProduit(
        ProduitRepository $produitRepository,ProduitBySizeRepository $produitBySizeRepo,
        TailleRepository $tailleRepository,CategorieRepository $categorieRepository,
        PromotionsRepository $promotionsRepository, Request $request, FunctionErrors $errorsCode
    ):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $errorsCode->generateCodeError002();
        }

        $produit = new Produit();
        /* Récupération de toutes les tailles en bdd */
        $tailles = $tailleRepository->findTypeSyze($data["typeTaille"]);
        /* Récupération de la catégorie demandée par rapport à son id en bdd */
        $categorie = $categorieRepository->findOneById($data['categorie']);
        $promo = $promotionsRepository->find($data['promotion']);
        /* Donnation des valeurs aux attributs du produit */
        $produit->setName($data['name']);
        $produit->setDescription($data['description']);
        $produit->setPathImage($data['infoFile']);
        $produit->setPrice(floatval($data['price']));

        /* Vérifier si is_trend est bien un boolean */
        if ($data['trend'] === true) {
            $produit->setIsTrend(true);
        }elseif ($data['trend'] === false) {
            $produit->setIsTrend(false);
        }else {
            return new JsonResponse([
                "errorCode" => "004",
                "errorMessage" => "is_trend is not boolean !"
            ], 406);
        }
        /* Vérifier si is_available est bien un boolean */
        if ($data['available'] === true) {
            $produit->setIsAvailable(true);
        }elseif ($data['available'] === false) {
            $produit->setIsAvailable(false);
        }else {
            return new JsonResponse([
                "errorCode" => "005",
                "errorMessage" => "is_available is not boolean !"
            ], 406);
        }
        /* Vérifier si la catégorie existe bien en bdd pour faire la relation */
        if (!$categorie) {
            return new JsonResponse([
                "errorCode" => "003",
                "errorMessage" => "la catégorie n'éxiste pas !"
            ], 404);
        }else {
            $produit->addCategory($categorie[0]);
        }
        /* Vérifier si la promotion existe bien en bdd pour faire la relation */
        if ($data['promotion'] === '-') {
            $produit->setPromotions(null);
        } elseif (!$promo) {
            return new JsonResponse([
                "errorCode" => "007",
                "errorMessage" => "la promotion n'existe pas !"
            ], 404);
        } else {
            $produit->setPromotions($promo);
        }
        /* Première insertion en bdd pour le produit */
        $produitRepository->save($produit, true);

        foreach ($tailles as $taille) {
            $produitBySize = new ProduitBySize();
            $produitBySize->setTaille($taille);
            $produitBySize->setStock(floatval($data["stock"][$taille->getTaille()]));
            $produitBySize->setProduit($produit);
            $produit->addProduitBySize($produitBySize);
            $produitBySizeRepo->save($produitBySize, true);
        }
        /* Deuxième insertion en bdd pour effectuer la relation des tailles pour le produit crée */
        $produitRepository->save($produit, true);

        $produitArray = [
            "id" => $produit->getId(),
            "name" => $produit->getName(),
            "res" => dump($data)
        ];

        return new JsonResponse($produitArray);
    }

    /**
     * @param ProduitRepository $produitRepository
     * @param Request $request
     * @param CategorieRepository $categorieRepository
     * @param PromotionsRepository $promotionsRepository
     * @param ProduitBySizeRepository $produitBySizeRepo
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/update',
        name: 'app_update_product', methods: "POST")]
    public function updateProduit(
        ProduitRepository $produitRepository, Request $request,CategorieRepository $categorieRepository,PromotionsRepository
        $promotionsRepository,ProduitBySizeRepository $produitBySizeRepo):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $produit = $produitRepository->find($data["id"]);

        if (!$produit) {
            return new JsonResponse([
                "errorCode" => "002",
                "errorMessage" => "Le produit n'existe pas !"
            ], 404);
        }

        $categorie = $categorieRepository->find($data['categorie']);
        $promotion = $promotionsRepository->find($data['promotion']);

        $produit->setName($data['name']);
        $produit->setDescription($data['description']);
        $produit->setPathImage($data['infoFile']);
        $produit->setPrice(floatval($data['price']));

        if ($data['trend'] === true) {
            $produit->setIsTrend(true);
        }elseif ($data['trend'] === false) {
            $produit->setIsTrend(false);
        }else {
            return new JsonResponse([
                "errorCode" => "004",
                "errorMessage" => "is_trend is not boolean !"
            ], 406);
        }
        if ($data['available'] === true) {
            $produit->setIsAvailable(true);
        }elseif ($data['available'] === false) {
            $produit->setIsAvailable(false);
        }else {
            return new JsonResponse([
                "errorCode" => "005",
                "errorMessage" => "is_available is not boolean !"
            ], 406);
        }
        if (!$categorie && ($data['categorie'] !== '-')) {
            return new JsonResponse([
                "errorCode" => "003",
                "errorMessage" => "La catégorie n'existe pas !"
            ], 404);
        }else {
            foreach ($produit->getCategories() as $cat) {
                $produit->removeCategory($cat);
            }
            if ($data['categorie'] !== '-') {
                $produit->addCategory($categorie);
            }
        }
        if ($data['promotion'] === '-') {
            $produit->setPromotions(null);
        }elseif (!$promotion) {
            return new JsonResponse([
                "errorCode" => "007",
                "errorMessage" => "La promotion n'existe pas !"
            ], 404);
        }else {
            $produit->setPromotions($promotion);
        }

        $produitRepository->save($produit, true);

        foreach ($produit->getProduitBySize() as $ps) {
            $ps->setStock(floatval($data['stock'][$ps->getTaille()->getTaille()]));
            $produitBySizeRepo->save($ps, true);
        }
        $produitRepository->save($produit, true);

        $produitArray = [
            "id" => $produit->getId(),
            "name" => $produit->getName()
        ];

        return new JsonResponse($produitArray, 200);
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
    #[Route('/update/trend/{id}/{trendBool}/',
        name: 'app_update_product_trend', methods: "POST")]
    public function updateTrendProduit(ProduitRepository $produitRepository, Request $request) : JsonResponse
    {
        $produit = $produitRepository->find($request->attributes->get('id'));

        if (!$produit){
            return new JsonResponse([
                "errorCode" => "002",
                "errorMessage" => "Le produit n'existe pas !"
            ],404);
        }

        if ($request->attributes->get('trendBool') === "true"){
            $produit->setIsTrend(true);
        }elseif ($request->attributes->get('trendBool') === "false"){
            $produit->setIsTrend(false);
        }else {
            return new JsonResponse([
                "errorCode" => "004",
                "errorMessage" => "trendBool is not boolean !"
            ],406);
        }

        $produitRepository->save($produit,true);

        $produitArray = [
            "id" => $produit->getId(),
            "name" => $produit->getName()
        ];

        return new JsonResponse($produitArray,200);
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
    #[Route('/update/multipleTrend/{trendBool}/',
        name: 'app_update_multiple_product_trend', methods: "POST")]
    public function updateMultipleTrendProduit(ProduitRepository $produitRepository, Request $request) : JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($data as $id) {
            $produit = $produitRepository->find($id);
            $bool = $request->attributes->get('trendBool');

            if (!$produit){
                return new JsonResponse([
                    "errorCode" => "002",
                    "errorMessage" => "Le produit n'existe pas !"
                ],404);
            }
    
            if ($bool === "true"){
                $produit->setIsTrend(true);
            }elseif ($bool === "false"){
                $produit->setIsTrend(false);
            }else {
                return new JsonResponse([
                    "errorCode" => "004",
                    "errorMessage" => "trendBool is not boolean !"
                ],406);
            }
    
            $produitRepository->save($produit,true);
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
    #[Route('/update/multipleAvailable/{availableBool}/',
        name: 'app_update_multiple_product_available', methods: "POST")]
    public function updateMultipleAvailableProduit(ProduitRepository $produitRepository, Request $request) : JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($data as $id) {
            $produit = $produitRepository->find($id);
            $bool = $request->attributes->get('availableBool');

            if (!$produit){
                return new JsonResponse([
                    "errorCode" => "002",
                    "errorMessage" => "Le produit n'existe pas !"
                ],404);
            }

            if ($bool === "true"){
                $produit->setIsAvailable(true);
            }elseif ($bool === "false"){
                $produit->setIsAvailable(false);
            }else {
                return new JsonResponse([
                    "errorCode" => "005",
                    "errorMessage" => "availableBool is not boolean !"
                ],406);
            }

            $produitRepository->save($produit,true);
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
    #[Route('/update/available/{id}/{availableBool}/',
        name: 'app_update_product_available', methods: "POST")]
    public function updateAvailableProduit(ProduitRepository $produitRepository, Request $request) : JsonResponse
    {
        $produit = $produitRepository->find($request->attributes->get('id'));

        if (!$produit){
            return new JsonResponse([
                "errorCode" => "002",
                "errorMessage" => "Le produit n'existe pas !"
            ],404);
        }

        if ($request->attributes->get('availableBool') === "true"){
            $produit->setIsAvailable(true);
        }elseif ($request->attributes->get('availableBool') === "false"){
            $produit->setIsAvailable(false);
        }else {
            return new JsonResponse([
                "errorCode" => "005",
                "errorMessage" => "availableBool is not boolean !"
            ],406);
        }

        $produitRepository->save($produit,true);

        $produitArray = [
            "id" => $produit->getId(),
            "name" => $produit->getName()
        ];

        return new JsonResponse($produitArray,200);
    }


    /**
     * @param ProduitRepository $produitRepository
     * @param Request $request
     * @param FunctionErrors $errorsCodes
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
    public function removeProduit(
        ProduitRepository $produitRepository,
        Request $request,
        FunctionErrors $errorsCodes,
        ProduitBySizeRepository $produitBySizeRepository,
        NoteRepository $noteRepository
    ):JsonResponse
    {
        $produit = $produitRepository->findOneById($request->attributes->get('id'));
        if (!$produit) {
            return new JsonResponse([
                "errorCode" => "002",
                "errorMessage" => "le produit n'éxiste pas !"
            ], 404);
        }else {
           $produit = $produit[0][0];
        }
        $arrayCommande = [];
        foreach ($produit->getProduitInCommande() as $pc) {
            $arrayCommande[] = $pc->getCommande()->getEtatCommande();
        }

        if (in_array("En préparation", $arrayCommande)) {
            return $errorsCodes->generateCodeError018();
        }else {
            foreach ($produit->getProduitBySize() as $size) {
                $produitBySizeRepository-> remove($size, true);
            }
            foreach ($produit->getNote() as $note) {
                $noteRepository-> remove($note, true);
            }
            foreach ($produit->getProduitInCommande() as $pc) {
                $pc->setProduit(null);
            }
            $produitRepository->remove($produit, true);
        }

        return new JsonResponse(null, 200);
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

    #[Route('/filter/{orderby}/{moyenne}/{minprice}/{maxprice}/{idCategorie}/{size}/{limit}/{offset}', name: 'app_filter_product', methods: "POST")]
    public function searchFilter(ProduitRepository $produitRepository,Request $request):JsonResponse
    {
        $produits = $produitRepository->findByFilter($request->attributes->get("orderby"),$request->attributes->get("moyenne"),$request->attributes->get("minprice"),$request->attributes->get("maxprice"),$request->attributes->get("idCategorie"),$request->attributes->get('size'),$request->attributes->get('limit'),$request->attributes->get("offset"));
        $countProduits = $produitRepository->countByFilter($request->attributes->get("orderby"),$request->attributes->get("moyenne"),$request->attributes->get("minprice"),$request->attributes->get("maxprice"),$request->attributes->get("idCategorie"),$request->attributes->get('size'));
        $produitArray = [];
        foreach($produits as $produit){
            $jsonProduct = [
                'id' => $produit[0]->getId(),
                'name' => $produit[0]->getName(),
                'description' => $produit[0]->getDescription(),
                'pathImage' => $produit[0]->getPathImage(),
                'price' => round($produit[0]->getPrice(), 2),
                'is_trend' => $produit[0]->isIsTrend(),
                'is_available' => $produit[0]->isIsAvailable(),
                "stockBySize" => array(),
                'noteAverage' => $produit[1] !== null ? round($produit[1],1) : $produit[1],
                'id_categorie' => $produit[0]->getCategories()[0] === null ? "-" : $produit[0]->getCategories()[0]->getId(),
                'promotion' =>
                    $produit[0]->getPromotions() !== null ? [
                        'id' => $produit[0]->getPromotions()->getId(),
                        'remise' => $produit[0]->getPromotions()->getRemise(),
                        'price_remise' => round($produit[0]->getPrice() - (($produit[0]->getPrice() * $produit[0]->getPromotions()->getRemise())/ 100), 2),
                        'date_start' => $produit[0]->getPromotions()->getDateStart()->format("d-m-Y"),
                        'heure_start' => $produit[0]->getPromotions()->getDateStart()->format("H:i:s"),
                        'date_end' => $produit[0]->getPromotions()->getDateEnd()->format("d-m-Y"),
                        'heure_end' => $produit[0]->getPromotions()->getDateEnd()->format("H:i:s"),
                    ] : [],
            ];
            foreach ($produit[0]->getProduitBySize() as $size){
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
                'id' => $product[0]->getId(),
                'name' => $product[0]->getName(),
                'description' => $product[0]->getDescription(),
                'pathImage' => $product[0]->getPathImage(),
                'price' => round($product[0]->getPrice(), 2),
                'is_trend' => $product[0]->isIsTrend(),
                'is_available' => $product[0]->isIsAvailable(),
                'stockBySize' => array(),
                'noteAverage' => $product[1] !== null ? round($product[1],1) : $product[1],
                'id_categorie' => $product[0]->getCategories()[0] === null ? "-" : $product[0]->getCategories()[0]->getId(),
                'promotion' =>
                    $product[0]->getPromotions() !== null ? [
                        'id' => $product[0]->getPromotions()->getId(),
                        'remise' => $product[0]->getPromotions()->getRemise(),
                        'price_remise' => round($product[0]->getPrice() - (($product[0]->getPrice() * $product[0]->getPromotions()->getRemise())/ 100), 2),
                        'date_start' => $product[0]->getPromotions()->getDateStart()->format("d-m-Y"),
                        'heure_start' => $product[0]->getPromotions()->getDateStart()->format("H:i:s"),
                        'date_end' => $product[0]->getPromotions()->getDateEnd()->format("d-m-Y"),
                        'heure_end' => $product[0]->getPromotions()->getDateEnd()->format("H:i:s"),
                    ] : [],
            ];
            foreach ($product[0]->getProduitBySize() as $size){
                $jsonProduct['stockBySize'][] = [
                    "taille" =>$size->getTaille()->getTaille(),
                    "stock" =>$size->getStock(),
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
    #[Route('/isTrend', name: 'produit_is_trend', methods: ['POST'])]
    public function searchProduitIsTrend(ProduitRepository $produitRepository): JsonResponse
    {
        $produits =  $produitRepository->getProduitIsTrend();
        if (count($produits) >= 2) {
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
        } else {
            return new JsonResponse([],200);
        }  
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
        if(count($produit)> 0) {
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
        } else {
            return new JsonResponse([null],200);
        }
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
    #[Route('/multipleRemove', name: 'app_multiple_delete_product', methods: "POST")]
    public function multipleRemoveProduit(ProduitRepository $produitRepository, Request $request, ProduitBySizeRepository $produitBySizeRepository, NoteRepository $noteRepository):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach($data as $id) {
            $produit = $produitRepository->findOneById($id);
            if (!$produit){
                return new JsonResponse([
                    "errorCode" => "002",
                    "errorMessage" => "le produit n'éxiste pas !"
                ],404);
            }else{
               $produit = $produit[0][0];
            }

            if ($produit->getProduitInCommande()[0] === null){
                foreach ($produit->getProduitBySize() as $size ){
                    $produitBySizeRepository-> remove($size, true);
                }
                foreach ($produit->getNote() as $note){
                    $noteRepository-> remove($note, true);
                }
                $produitRepository->remove($produit, true);
            } else {
                return new JsonResponse([
                    "errorCode" => "006",
                    "errorMessage" => "Le produit est en cours de commande !"
                ],404);
            }
        }
        return new JsonResponse(null,200);
    }
}

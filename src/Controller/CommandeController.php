<?php

namespace App\Controller;

use App\GlobalFunction\FunctionErrors;
use App\Repository\CommandeRepository;
use App\Repository\ProduitInCommandeRepository;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Commande;
use App\Entity\ProduitInCommande;
use App\Repository\ProduitRepository;
use App\Repository\TailleRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;

#[Route('/api/commande')]
class CommandeController extends AbstractController
{

    /**
     * @param CommandeRepository $commandeRepository
     * @param ProduitInCommandeRepository $produitInCommandeRepository
     * @return JsonResponse
     * @OA\Tag (name="Commande")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/', name: 'app_commande', methods:"GET")]
    public function findAll(
        CommandeRepository $commandeRepository,
        ProduitInCommandeRepository $produitInCommandeRepository,
    ): JsonResponse
    {
        $commandes = $commandeRepository->findAll();
        if (!$commandes) {
                return new JsonResponse([
                    "errorCode" => "002",
                    "errorMessage" => "Aucune commande n'éxiste !"
                ], 404);
        }else {
            $commandeArray = [];
            foreach ($commandes as $commande) {
                $jsonCommande = [
                    'id' => $commande->getId(),
                    'user' => [
                        'user_id' => $commande->getUser()->getId(),
                        'user_name' => $commande->getUser()->getName(),
                        'user_surname' => $commande->getUser()->getSurname(),
                        'user_email' => $commande->getUser()->getEmail(),
                        'user_phone' => $commande->getUser()->getPhone() ? $commande->getUser()->getPhone() : "null",
                    ],
                    'adresse' => [
                        'num_rue' => $commande->getNumRueLivraison(),
                        'complement_adresse' => $commande->getComplementAdresseLivraison(),
                        'rue' => $commande->getRueLivraison(),
                        'ville' => $commande->getVilleLivraison(),
                        'code_postal' => $commande->getCodePostalLivraison(),
                    ],
                    'date_facture' => $commande->getDateFacture()->format("d-m-Y"),
                    'heure_facture' => $commande->getDateFacture()->format('H:i:s'),
                    'etatCommande' => $commande->getEtatCommande(),
                    'produitInCommande' => array(),
                    'totalCommande' => null
                ];
                $i = 0;
                $produitsInCommande = $produitInCommandeRepository->findOneOrderbyIdCommandes($commande->getId());
                $totalCommande=0;
                foreach ($produitsInCommande as $produitInCommande) {

                    $totalCommande += $produitInCommande->getQuantite() * $produitInCommande->getPrice();

                    $jsonCommande['produitInCommande'][] = [
                        "id" =>$produitInCommande->getId(),
                        "produit_id" =>
                            $produitInCommande->getProduit() ? $produitInCommande->getProduit()->getId() : null,
                        "taillesBySyze" => array(),
                        "taille_produit" => array(),
                        'quantite' => $produitInCommande->getQuantite(),
                        'price' => $produitInCommande->getPrice(),
                        'name' => $produitInCommande->getNameProduct(),
                        'prix_remise' => $produitInCommande->getPrice() * $produitInCommande->getRemise()/100,
                        'remise' => $produitInCommande->getRemise(),
                        'total' => $produitInCommande->getQuantite() * $produitInCommande->getPrice(),
                        'taille' => $produitInCommande->getTaille(),
                        'image' =>
                            $produitInCommande->getProduit() ? $produitInCommande->getProduit()->getPathImage(): null,
                    ];
                    if ($produitInCommande->getProduit()) {
                        foreach ($produitInCommande->getProduit()->getProduitBySize() as $size) {
                            $jsonCommande['produitInCommande'][$i]["taille_produit"][] = [
                                "taille_id" => $size->getTaille()->getId(),
                                "taille" => $size->getTaille()->getTaille(),
                                "stock" => $size->getStock(),
                            ];
                        }
                    }
                    $i++;
                }
                $jsonCommande ['totalCommande'][] = $totalCommande;
                $commandeArray[] = $jsonCommande;
            }
        }
        return new JsonResponse($commandeArray);
    }

    /**
     * @param CommandeRepository $commandeRepository
     * @param ProduitInCommandeRepository $produitInCommandeRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Commande")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/update/{orderId}/{rue}/{num_rue}/{complement_adresse}/{code_postal}/{ville}/{etat_commande}',
        name: 'app_update_commande', methods:"PUT")]
    public function updateOrder(
        CommandeRepository $commandeRepository,
        ProduitInCommandeRepository $produitInCommandeRepository,
        Request $request
    ): JsonResponse
    {
        $order = $commandeRepository->find($request->attributes->get('orderId'));

        $order->setRueLivraison($request->attributes->get('rue'));
        $order->setNumRueLivraison($request->attributes->get('num_rue'));
        $order->setVilleLivraison($request->attributes->get('ville'));
        if ($request->attributes->get('complement_adresse' ) != 'null') {
            $order->setComplementAdresseLivraison($request->attributes->get('complement_adresse'));
        } else {
            $order->setComplementAdresseLivraison(null);
        }
        
        $order->setCodePostalLivraison($request->attributes->get('code_postal'));
        $order->setEtatCommande($request->attributes->get('etat_commande'));
        
        $commandeRepository->save($order, true);
        $orderArray = [
            "rue" => $order->getRueLivraison(),
            "num_rue" => $order->getNumRueLivraison(),
            "complement_adresse" => $order->getComplementAdresseLivraison(),
            "code_postal" => $order->getCodePostalLivraison(),
            "etat_commande" => $order->getEtatCommande()
        ];

        return new JsonResponse($orderArray);
    }

    /**
     * @param CommandeRepository $commandeRepository
     * @param FunctionErrors $errorsCodes
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Commande")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/cancel/{id}', name: 'app_cancel_commande', methods:"PUT")]
    public function cancelOrder(
        CommandeRepository $commandeRepository,
        FunctionErrors $errorsCodes,
        Request $request
    ): JsonResponse
    {
        $order = $commandeRepository->find($request->attributes->get('id'));

        if ($order->getEtatCommande() === "En cours de livraison") {
            return $errorsCodes->generateCodeError016();
        }elseif ($order->getEtatCommande() === "Livrée") {
            return $errorsCodes->generateCodeError017();
        }

        $order->setEtatCommande("Annulée");
        
        $commandeRepository->save($order, true);
        $orderArray = [
            "id" => $order->getId()
        ];

        return new JsonResponse($orderArray);
    }

    /**
     * @param CommandeRepository $commandeRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Commande")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/byUser/{id}', name: 'app_commandes_by_user', methods:"POST")]
    public function findAllCommandesByUser(
        CommandeRepository $commandeRepository, Request $request
    ): JsonResponse
    {
        $commandesUser = $commandeRepository->findByUserId($request->attributes->get("id"));
        if (!$commandesUser) {
            return new JsonResponse([], 200);
        }else {
            $commandesByUserArray = [];
            foreach ($commandesUser as $oneCommande) {
                $total = 0;
                $jsonCommande = [
                    'id' => $oneCommande->getId(),
                    'user' => [
                        'user_id' => $oneCommande->getUser()->getId(),
                    ],

                    'date_facture' => $oneCommande->getDateFacture()->format("d-m-Y"),
                    'heure_facture' => $oneCommande->getDateFacture()->format('H:i:s'),
                    'etatCommande' => $oneCommande->getEtatCommande(),
                    'montant' => $total
                ];
                foreach ($oneCommande->getProduitInCommande() as $pc) {
                    $total += $pc->getRemise() ? $pc->getQuantite() * (
                              $pc->getPrice() - $pc->getPrice() * $pc->getRemise()/100)
                            : $pc->getQuantite() * $pc->getPrice();
                }
                $jsonCommande["montant"]=$total;

                $commandesByUserArray[] = $jsonCommande;
            }
        }
        return new JsonResponse($commandesByUserArray);
    }

    /**
     * @param CommandeRepository $commandeRepository
     * @param FunctionErrors $errorsCodes
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Commande")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/multipleCancel', name: 'app_multiple_cancel_commande', methods:"PUT")]
    public function multipleCancelOrder(
        CommandeRepository $commandeRepository,
        FunctionErrors $errorsCodes,
        Request $request
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach($data as $id) {
            $order = $commandeRepository->find($id);

            if ($order->getEtatCommande() === "En cours de livraison") {
                return $errorsCodes->generateCodeError016();
            }elseif ($order->getEtatCommande() === "Livrée") {
                return $errorsCodes->generateCodeError017();
            }

            $order->setEtatCommande("Annulée");
            $commandeRepository->save($order, true);
        }

        return new JsonResponse(null,200);
    }

    /**
     * @param CommandeRepository $commandeRepository
     * @param FunctionErrors $errorsCodes
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Commande")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/countMonth', name: 'commande_count', methods: "GET")]
    public function countCommandeMonth(CommandeRepository $commandeRepository):JsonResponse{

        $date = new \DateTime();
        $startDate = $date->format('Y/m/01');
        $endDate = $date->format('Y/m/t');

        $countCommandeMonth = $commandeRepository->countMonth($startDate,$endDate);

        return new JsonResponse($countCommandeMonth[0]);
    }


        /**
     * @param CommandeRepository $commandeRepository
     * @param FunctionErrors $errorsCodes
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Commande")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */

     #[Route('/recentCommande', name: 'commande_recent', methods: "GET")]
     public function recentCommande(CommandeRepository $commandeRepository):JsonResponse{
 
 
        $recentCommande = $commandeRepository->recentCommande();
        $commandeArray = [];
        
        foreach($recentCommande as $commande){
            $total=0;

            $commandes=[
                'user_name' => $commande->getUser()->getName(),
                "dateFacture"=>$commande->getDateFacture(),
                "price"=> [],
                "etat"=>$commande->getEtatCommande(),
            ];
            foreach ($commande->getProduitInCommande() as $pc) {
                $total += $pc->getRemise() ? $pc->getQuantite() * (
                          $pc->getPrice() - $pc->getPrice() * $pc->getRemise()/100)
                        : $pc->getQuantite() * $pc->getPrice();
            }
            $commandes["price"]=round($total, 2);
            $commandeArray[]=$commandes;
        }
        return new JsonResponse($commandeArray);
 
     }

     
        /**
     * @param CommandeRepository $commandeRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Commande")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */

     #[Route('/createCommande', name: 'commande_create', methods: "POST")]
     public function createCommande(CommandeRepository $commandeRepository,UserRepository $userRepository,ProduitInCommandeRepository $produitInCommandeRepository,ProduitRepository $produitRepository, Request $request):JsonResponse{
        $data=json_decode($request->getContent(),true);
 
        $order = new Commande();

        $order->setUser($userRepository->find($data['userId']));
        $order->setRueLivraison($data['rueLivraison']);
        $order->setNumRueLivraison($data['num_rueLivraison']);
        $order->setVilleLivraison($data['villeLivraison']);
        $order->setRueFacturation($data['rueFacturation']);
        $order->setNumRueFacturation($data['num_rueFacturation']);
        $order->setVilleFacturation($data['villeFacturation']);
        $order->setComplementAdresseLivraison($data['complement_adresseLivraison']);
        $order->setComplementAdresseFacturation($data['complement_adresseFacturation']);
        $order->setCodePostalLivraison($data['code_postalLivraison']);
        $order->setCodePostalFacturation($data['code_postalFacturation']);
        $order->setEtatCommande($data['etat_commande']);
        
        $commandeRepository->save($order, true);
        
        foreach($data['produits'] as $produit){
            $produitInCommande = new ProduitInCommande();

            $produitOrder = $produitRepository->find($produit['id']);

            $produitInCommande->setProduit($produitOrder);
            $produitInCommande->setCommande($order);
            $produitInCommande->setQuantite($produit['quantity']);
            $produitInCommande->setPrice($produitOrder->getPrice());
            $produitInCommande->setRemise($produitOrder->getPromotions() ? $produitOrder->getPromotions()->getRemise() : 0);
            $produitInCommande->setTaille($produit['size']);
            $produitInCommande->setNameProduct($produit['name']);

            $produitInCommandeRepository->save($produitInCommande, true);
        };

        $orderArray = [
            "id" => $order->getId(),
        ];
        return new JsonResponse($orderArray);
 
     }

}

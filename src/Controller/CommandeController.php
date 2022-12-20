<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\ProduitInCommandeRepository;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
        ProduitInCommandeRepository $produitInCommandeRepository
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
                        'num_rue' => $commande->getNumRue(),
                        'complement_adresse' => $commande->getComplementAdresse(),
                        'rue' => $commande->getRue(),
                        'ville' => $commande->getVille(),
                        'code_postal' => $commande->getCodePostal(),
                    ],
                    'date_facture' => $commande->getDateFacture()->format("d-m-Y"),
                    'heure_facture' => $commande->getDateFacture()->format('H:i:s'),
                    'etatCommande' => $commande->getEtatCommande(),
                    'produitInCommande' => array(),
                ];

                $produitsInCommande = $produitInCommandeRepository->findOneOrderbyIdCommandes($commande->getId());

                foreach ($produitsInCommande as $produitInCommande) {
                    $jsonCommande['produitInCommande'][] = [
                        "id" =>$produitInCommande->getId(),
                        'quantite' => $produitInCommande->getQuantite(),
                        'prixUnitaire' => $produitInCommande->getPrice(),
                        'nomProduit' => $produitInCommande->getProduit()->getName(),
                        'remise' => $produitInCommande->getPrice() * $produitInCommande->getRemise()/100,
                        'remise en %' => $produitInCommande->getRemise(),
                        'total' => $produitInCommande->getQuantite() * $produitInCommande->getPrice(),
                        'Taille' => $produitInCommande->getTaille(),
                        'image' => $produitInCommande->getProduit()->getPathImage(),
                    ];
                }

                $commandeArray[] = $jsonCommande;
            }
        }
        return new JsonResponse($commandeArray);
    }
    // RECUPERATION DES COMMANDES D UN UTILISATEUR
        /**
     * @param CommandesUserRepository $commandeRepository
     * @param commandesUserRepository $produitInCommandeRepository
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
                return new JsonResponse([
                    "errorCode" => "002",
                    "errorMessage" => "Aucune commande n'existe !"
                ], 404);
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
                foreach($oneCommande->getProduitInCommande() as $pc){
                    $total += $pc->getPrice();
                }
                $jsonCommande["montant"]=$total;

                $commandesByUserArray[] = $jsonCommande;
            }
        }
        return new JsonResponse($commandesByUserArray);
    }

}

<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\ProduitInCommandeRepository;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

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
                    'totalCommande' => null
                ];

                $produitsInCommande = $produitInCommandeRepository->findOneOrderbyIdCommandes($commande->getId());
                $totalCommande=0;
                foreach ($produitsInCommande as $produitInCommande) {
                    $totalCommande += $produitInCommande->getQuantite() * $produitInCommande->getPrice();
                    $jsonCommande['produitInCommande'][] = [
                        "id" =>$produitInCommande->getId(),
                        'quantite' => $produitInCommande->getQuantite(),
                        'price' => $produitInCommande->getPrice(),
                        'name' => $produitInCommande->getProduit()->getName(),
                        'prix_remise' => $produitInCommande->getPrice() * $produitInCommande->getRemise()/100,
                        'remise' => $produitInCommande->getRemise(),
                        'total' => $produitInCommande->getQuantite() * $produitInCommande->getPrice(),
                        'taille' => $produitInCommande->getTaille(),
                        'image' => $produitInCommande->getProduit()->getPathImage(),
                    ];
                }
                $jsonCommande ['totalCommande'] = $totalCommande; 
                $commandeArray[] = $jsonCommande;
            }
        }
        return new JsonResponse($commandeArray);
    }
}

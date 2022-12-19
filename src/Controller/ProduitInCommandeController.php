<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use App\Repository\ProduitInCommandeRepository;
use Symfony\Component\HttpFoundation\Request;

#[Route('api/produitInCommande')]
class ProduitInCommandeController extends AbstractController {

    /**
     * @param ProduitInCommandeRepository $produitInCommandeRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="ProduitInCommande")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/single_order/{idCommande}', name: 'produit_in_commande', methods:"POST")]
    public function singleOrder(
        ProduitInCommandeRepository $produitInCommandeRepository,
        Request $request
    ): JsonResponse
    {
        $produitInCommandes = $produitInCommandeRepository->
        findOneOrderbyIdCommandes($request->attributes->get('idCommande'));
        
        $produitInCommandeArray = [];
        foreach ($produitInCommandes as $produitInCommande) {
            $produitInCommandeArray[] = [
                'id' => $produitInCommande->getId(),
                'quantite' => $produitInCommande->getQuantite(),
                'prixUnitaire' => $produitInCommande->getPrice(),
                'nomProduit' => $produitInCommande->getProduit()->getName(),
                'remise' => $produitInCommande->getPrice() * $produitInCommande->getRemise()/100,
                'remise en %' => $produitInCommande->getRemise(),
                'total' => $produitInCommande->getQuantite() * $produitInCommande->getPrice(),
                'Taille' => $produitInCommande->getTaille(),
                'image' => $produitInCommande->getProduit()->getPathImage(),
            ];
            if (end($produitInCommandes)=== $produitInCommande) {
            $infosCommandes[] = [
                'dateFacture' => $produitInCommande->getCommande()->getDateFacture()->format("d-m-Y"),
                'numeroCommande' => $produitInCommande->getCommande()->getId(),
                'Etat' => $produitInCommande->getCommande()->getEtatCommande(),
                'Adresse' => [
                    'ville' => $produitInCommande->getCommande()->getVille(),
                    'rue' => $produitInCommande->getCommande()->getRue(),
                    'Code_Postal' => $produitInCommande->getCommande()->getCodePostal()
                ]
            ];
            $produitInCommandeArray[] = $infosCommandes;
        }
    }
        return new JsonResponse($produitInCommandeArray);
    }
}

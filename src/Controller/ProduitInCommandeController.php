<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use App\Repository\ProduitInCommandeRepository;
use Symfony\Component\HttpFoundation\Request;

#[Route('api/produitInCommande')]
class ProduitInCommandeController extends AbstractController{
        /**
     * @param ProduitInCommandeRepository $ProduitInCommandeRepository
     * @return JsonResponse
     * @OA\Tag (name="ProduitInCommande")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/produitInCommande/{idCommande}', name: 'produit_in_commande', methods:"POST")]
    public function ProduitInCommande(ProduitInCommandeRepository $produitInCommandeRepository, Request $request): JsonResponse
    {
        $produitInCommandes = $produitInCommandeRepository->findOneOrderbyIdCommandes($request->attributes->get('idCommande'));
        
        $produitInCommandeArray = [];
        foreach($produitInCommandes as $produitInCommande){
            $produitInCommandeArray[] = [
                'id' => $produitInCommande->getId(),
                'quantite' => $produitInCommande->getQuantite(),
                'prixUnitaire' => $produitInCommande->getPrice(),
                'nomProduit' => $produitInCommande->getProduit()->getName(),
                'remise' => $produitInCommande->getPrice() * $produitInCommande->getProduit()->getPromotions()->getRemise()/100,
                'remise en %' => $produitInCommande->getProduit()->getPromotions()->getRemise(),
                'total' => $produitInCommande->getQuantite() * $produitInCommande->getPrice(),
                'numeroCommande' => $produitInCommande->getCommande()->getId(),
                'dateFacture' => $produitInCommande->getCommande()->getDateFacture()->format("d-m-Y"),
                'Etat' => $produitInCommande->getCommande()->getEtatCommande(),
                'Nom User' => $produitInCommande->getCommande()->getUser()->getAdresse()->getRue()
            ];
        }
        return new JsonResponse($produitInCommandeArray);
    }
}

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
        $loadCommande = $produitInCommandeRepository->findOneOrderbyIdCommandes($request->attributes->get('idCommande'));
        
        $produitInCommandeArray = [];
        foreach($loadCommande as $commande){
            $produitInCommandeArray[] = [
            'id' => $commande->getId(),
            'quantite' => $commande->getQuantite(),
            'prixUnitaire' => $commande->getPrice(),
            'total' => $commande->getQuantite() * $commande->getPrice() 
            ];
        }
        return new JsonResponse($produitInCommandeArray);
    }
}

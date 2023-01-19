<?php

namespace App\Controller;

use App\Repository\NoteRepository;
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
     * @param NoteRepository $noteRepository
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
        NoteRepository $noteRepository,
        Request $request
    ): JsonResponse
    {
        $produitInCommandes = $produitInCommandeRepository->
        findOneOrderbyIdCommandes($request->attributes->get('idCommande'));
        $produitInCommandeArray = [];
        $note = 0;
        foreach ($produitInCommandes as $produitInCommande) {
            if ($produitInCommande->getCommande()->getEtatCommande() === "Livrée") {
                $note = $noteRepository->findNoteByUser(
                    $produitInCommande->getCommande()->getUser()->getId(),
                    $produitInCommande->getProduit()->getId()
                );
            }
            if (!$note) {
                $note = 0;
            }
            $produitInCommandeArray[] = [
                'id' => $produitInCommande->getId(),
                'quantite' => $produitInCommande->getQuantite(),
                'prixUnitaire' => $produitInCommande->getPrice(),
                'prixRemise' => $produitInCommande->getRemise() ? round($produitInCommande->getPrice() - (
                        $produitInCommande->getPrice() * $produitInCommande->getRemise()/100),2) : "-",
                'nomProduit' => $produitInCommande->getNameProduct(),
                'remise' => $produitInCommande->getRemise(),
                'total' => $produitInCommande->getRemise() ? $produitInCommande->getQuantite() * (
                    $produitInCommande->getPrice() - $produitInCommande->getPrice() * $produitInCommande->getRemise()/100)
                        : $produitInCommande->getQuantite() * $produitInCommande->getPrice(),
                'Taille' => $produitInCommande->getTaille(),
                'image' => $produitInCommande->getProduit() ? $produitInCommande->getProduit()->getPathImage() : "-",
                'produitId' => $produitInCommande->getProduit()->getId(),
                'noteByUser' => $note > 0 ? $note[0]->getNote() : 0
            ];

            if (end($produitInCommandes) === $produitInCommande) {
            $infosCommandes[] = [
                'dateFacture' => $produitInCommande->getCommande()->getDateFacture()->format("d-m-Y"),
                'numeroCommande' => $produitInCommande->getCommande()->getId(),
                'Etat' => $produitInCommande->getCommande()->getEtatCommande(),
                'Adresse' => [
                    'ville' => $produitInCommande->getCommande()->getVilleLivraison(),
                    'rue' => $produitInCommande->getCommande()->getRueLivraison(),
                    'Code_Postal' => $produitInCommande->getCommande()->getCodePostalLivraison()
                ]
            ];
            $produitInCommandeArray[] = $infosCommandes;
        }
    }
        return new JsonResponse($produitInCommandeArray);
    }


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
    #[Route('/single_order/delete/{id}', name: 'delete_produit_in_commande', methods:"DELETE")]
    public function deleteOneProduct(
        ProduitInCommandeRepository $produitInCommandeRepository,
        Request $request
    ): JsonResponse
    {
        $deleteProduitInCommande = $produitInCommandeRepository->find($request->attributes->get('id'));
        if ($deleteProduitInCommande != null) {
            $produitInCommandeRepository->remove($deleteProduitInCommande, true);
        }else {
            return new JsonResponse([
                'errorCode' => "013",
                'errorMessage' => "Ce produit n'existe pas"
            ], 409);
        }
        return new JsonResponse([
            'successCode' => "004",
            'successMessage' => "Le produit a bien été supprimé de la commande"
        ], 200);
    }
}

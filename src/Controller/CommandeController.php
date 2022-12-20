<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\ProduitInCommandeRepository;
use OpenApi\Annotations as OA;
use phpDocumentor\Reflection\Types\Null_;
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
    #[Route('/update/{orderId}/{rue}/{num_rue}/{complement_adresse}/{code_postal}/{ville}/{etat_commande}', name: 'app_update_commande', methods:"POST")]
    public function updateOrder(
        CommandeRepository $commandeRepository,
        ProduitInCommandeRepository $produitInCommandeRepository,
        Request $request
    ): JsonResponse
    {
        $order = $commandeRepository->find($request->attributes->get('orderId'));

        $order->setRue($request->attributes->get('rue'));
        $order->setNumRue($request->attributes->get('num_rue'));
        if($request->attributes->get('complement_adresse' )!= 'null'){
            $order->setComplementAdresse($request->attributes->get('complement_adresse'));    
        }else{
            $order->setComplementAdresse(null);
        };
        
        $order->setCodePostal($request->attributes->get('code_postal'));
        $order->setEtatCommande($request->attributes->get('etat_commande'));
        
        $commandeRepository->save($order, true);
        $orderArray = [
            "rue" => $order->getRue(),
            "num_rue" => $order->getNumRue(),
            "complement_adresse" => $order->getComplementAdresse(),
            "code_postal" => $order->getCodePostal(),
            "etat_commande" => $order->getEtatCommande()
        ];

        return new JsonResponse($orderArray);
    }

}

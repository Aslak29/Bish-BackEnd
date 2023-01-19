<?php

namespace App\Services;

use App\Entity\Notation;
use App\Repository\NoteRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class NoteService
{
    private NoteRepository $noteRepository;
    private UserRepository $userRepository;
    private ProduitRepository $produitRepository;


    /**
     * @param NoteRepository $noteRepository
     * @param UserRepository $userRepository
     * @param ProduitRepository $produitRepository
     */
    public function __construct(
        NoteRepository $noteRepository,
        UserRepository $userRepository,
        ProduitRepository $produitRepository
    )
    {
        $this->noteRepository = $noteRepository;
        $this->userRepository = $userRepository;
        $this->produitRepository = $produitRepository;
    }


    /*
      Cette function permet de vérifier si l'utilisateur peut noter le produit.
      L'utilisateur doit avoir commandé le produit et avoir ça commande livrée pour noter le produit.
    */
    public function updateNoteByUser($userId, $idProduct, $noteValue): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        $noteArray = [];
        foreach ($user->getCommandes() as $userCommande) {
            if ($userCommande->getEtatCommande() === "Livrée") {
                $productArray = [];
                foreach ($userCommande->getProduitInCommande() as $produitInCommande) {
                    $productArray[] = $produitInCommande->getProduit()->getId();
                }
                if (in_array($idProduct, $productArray)) {
                    $note = $this->noteRepository->findNoteByUser($userId, $idProduct);
                    $produit = $this->produitRepository->find($idProduct);
                    if ($note) {
                        $note[0]->setNote(floatval($noteValue));
                        $this->noteRepository->save($note[0], true);
                        $noteArray[] = [
                            "note" => $note[0]->getNote(),
                            "message" => "Votre note à bien été modifiée !"
                        ];
                    }else {
                        $note = new Notation();
                        $note->setNote(floatval($noteValue))->setUser($user)->setProduit($produit);
                        $this->noteRepository->save($note, true);
                        $noteArray[] = [
                            "note" => $note->getNote(),
                            "message" => "Votre note à bien été prise en compte !"
                        ];
                    }
                }else {
                    $noteArray = [];
                }
            }
        }

        return new JsonResponse($noteArray, 200);
    }
}

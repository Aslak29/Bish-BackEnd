<?php

namespace App\Controller;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit;

#[Route('api/notation')]
class NotationController extends AbstractController{
        /**
     * @param NoteRepository $produitRepository
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/note', name: 'note_produit', methods:"GET")]
    public function productNotation(NoteRepository $notesRepository): JsonResponse
    {
        $notes = $notesRepository->findAll();
        $noteArray = [];
        foreach($notes as $note){
            $noteArray[] = [
                'id' => $note->getId(),
                'name' => $note->getName(),

            ];
        }
        return new JsonResponse($noteArray);
    }
}
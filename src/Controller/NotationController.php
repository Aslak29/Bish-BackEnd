<?php

namespace App\Controller;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Services\NoteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use App\Entity\Produit;

#[Route('api/notation')]
class NotationController extends AbstractController
{
    private NoteService $noteService;


    /**
     * @param NoteService $noteService
     */
    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }


    /**
     * @param NoteRepository $notesRepository
     * @return JsonResponse
     * @OA\Tag (name="Note")
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
        foreach ($notes as $note) {
            $noteArray[] = [
                'id' => $note->getId(),
                'note' => $note->getNote(),
                'nameProduct' => $note->getProduit()->getName(),
            ];
        }
        return new JsonResponse($noteArray);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Note")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/noteUser/{id}/{idProduct}/{value}', name: 'note_update_produit', methods:"POST")]
    public function noteByUser(Request $request): JsonResponse
    {
        $userId = $request->attributes->get('id');
        $productId = $request->attributes->get('idProduct');
        $noteValue = $request->attributes->get('value');
        $note = $this->noteService->updateNoteByUser($userId, $productId, $noteValue);

        return new JsonResponse($note);
    }

}
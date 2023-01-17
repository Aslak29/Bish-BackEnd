<?php

namespace App\Services;

use App\Repository\NoteRepository;

class NoteService
{
    private NoteRepository $noteRepository;

    /**
     * @param NoteRepository $noteRepository
     */
    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }


    public function updateNoteByUser($userId, $idProduct, $noteValue): array
    {
        $note = $this->noteRepository->findNoteByUser($userId, $idProduct);

        if ($note) {
            $note[0]->setNote(floatval($noteValue));
            $this->noteRepository->save($note[0], true);
            $noteArray = ["note" => $note[0]->getNote()];
        }else {
            $noteArray = [];
        }

        return $noteArray;
    }
}
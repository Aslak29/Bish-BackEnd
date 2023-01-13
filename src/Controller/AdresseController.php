<?php

namespace App\Controller;

use App\Repository\AdresseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route('api/adresse')]
class AdresseController extends AbstractController
{
    /**
     * @param AdresseRepository $adresseRepository
     * @return JsonResponse
     * @OA\Tag (name="Adresse")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/', name: 'app_adresse', methods: "GET")]
    public function load(AdresseRepository $adresseRepository): JsonResponse{

        $adresses = $adresseRepository->findAll();
        $jsonAdresses = [];

        foreach ($adresses as $adress){
            $jsonAdresses[] = [
                'id' => $adress->getId(),
                'user_id' => $adress->getUser()->getId(),
                'city' => $adress->getCity(),
                'rue' => $adress->getRue(),
                'postal_code' => $adress->getPostalCode(),
                'num_rue' => $adress->getNumRue(),
                'complement_adresse' => $adress->getComplementAdresse(),
                'name' => $adress->getName()
            ];
        }
        return new JsonResponse($jsonAdresses,200);
    }

    /**
     * @param AdresseRepository $adresseRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Adresse")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/delete/{id}', name: 'app_delete_adresse', methods: "DELETE")]
    public function delete(AdresseRepository $adresseRepository, Request $request): JsonResponse{

        $adresse = $adresseRepository->find($request->attributes->get('id'));
        if ($adresse === null){
            return new JsonResponse([
                "errorMessage" => "L'adresse n'existe pas"
            ]);
        }else{
            $adresseRepository->remove($adresse,true);
            return new JsonResponse([
                "successMessage" => "adress has been remove"
            ],200);
        }
    }

}

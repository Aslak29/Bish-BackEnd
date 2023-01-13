<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Repository\AdresseRepository;
use App\Repository\UserRepository;
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

    /**
     * @param AdresseRepository $adresseRepository
     * @param Request $request
     * @return JsonResponse
     * * @OA\Tag (name="Adresse")
     *  @OA\Parameter(
     *     name="userId",
     *     in="query",
     *     description="Utilisateur liée à l'adresse",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag (name="Adresse")
     * * @OA\Parameter(
     *     name="city",
     *     in="query",
     *     description="Ville de l'adresse",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="rue",
     *     in="query",
     *     description="Rue de l'adresse",
     *     @OA\Schema(type="string")
     * )
     *  @OA\Parameter(
     *     name="postalCode",
     *     in="query",
     *     description="Code postal de l'adresse",
     *     @OA\Schema(type="string")
     * )
     *  @OA\Parameter(
     *     name="num_rue",
     *     in="query",
     *     description="Numéro de rue de l'adresse",
     *     @OA\Schema(type="string")
     * )
     *   @OA\Parameter(
     *     name="cpm_adresse",
     *     in="query",
     *     description="Complément d'adresse de l'adresse",
     *     @OA\Schema(type="string")
     * )
     *  @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Nom de l'adresse",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/create', name: 'app_route_adresse', methods: "POST")]
    public function create(AdresseRepository $adresseRepository, UserRepository $userRepository, Request $request):JsonResponse{

        $adresse = new Adresse();
        $adresse->setCity($request->query->get("city"));
        $adresse->setRue($request->query->get("rue"));
        $adresse->setNumRue($request->query->get("num_rue"));
        $adresse->setComplementAdresse($request->query->get("cpm_adresse"));
        $adresse->setName($request->query->get("name"));
        $adresse->setCity($request->query->get("city"));
        $adresse->setPostalCode(($request->query->get("postalCode")));
        $adresse->setUser($userRepository->find($request->query->get("userId")));

        $adresseRepository->save($adresse,true);

        return new JsonResponse();

    }

}

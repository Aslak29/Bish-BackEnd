<?php
namespace App\Controller;

use App\Entity\Adresse;
use App\Repository\AdresseRepository;
use App\Repository\UserRepository;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Security;

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
    #[Route('api/admin/adresse/', name: 'app_adresse', methods: "GET")]
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
    #[Route('/api/authenticated/adresse/delete/{id}', name: 'app_delete_adresse', methods: "DELETE")]
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
    #[Route('/api/authenticated/adresse/create', name: 'app_route_adresse', methods: "POST")]
    public function create(AdresseRepository $adresseRepository, UserRepository $userRepository, Request $request):JsonResponse{

        $data = json_decode($request->getContent(), true);

        $adresse = new Adresse();
        $adresse->setCity($data["city"]);
        $adresse->setRue($data["rue"]);
        $adresse->setNumRue($data["num_rue"]);
        $adresse->setComplementAdresse($data["cpm_adresse"]);
        $adresse->setName($data["name"]);
        $adresse->setPostalCode($data["postalCode"]);
        $adresse->setUser($userRepository->find($data["userId"]));

        $adresseRepository->save($adresse,true);

        return new JsonResponse();
    }

     /**
      * @param AdresseRepository $adresseRepository
      * @param Request $request
      * @return JsonResponse
      * @OA\Tag (name="Adresse")
      * @Security(name="Bearer")
      */
    #[Route('/api/authenticated/adresse/findByUser/{id}', name:'app_adresse_idUser', methods: "POST")]
    public function findByUserId(AdresseRepository $adresseRepository, UserRepository $userRepository, Request $request):JsonResponse{

        if ($userRepository->find($request->attributes->get('id')) === null){
            return new JsonResponse([
                "errorMessage" => "L'utilisateur n'existe pas"
            ]);
        }

        $adresses = $adresseRepository->findByUser($request->attributes->get('id'));
        $adressesJson = [];

        foreach ($adresses as $adress){
            $adressesJson[] = [
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

        return new JsonResponse($adressesJson,200);
    }

    /**
     * @param AdresseRepository $adresseRepository
     * @param Request $request
     * @return JsonResponse
     * * @OA\Tag (name="Adresse")
     * *  @OA\Parameter(
     *     name="adresseId",
     *     in="query",
     *     description="Adresse a changer",
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
    #[Route('/api/authenticated/adresse/update', name: 'app_route_adresse_update', methods: "PUT")]
    public function update(AdresseRepository $adresseRepository, UserRepository $userRepository, Request $request):JsonResponse{

        $data = json_decode($request->getContent(), true);

        $adresse = $adresseRepository->find($data['adresseId']);
        if ($adresse === null){
            return new JsonResponse([
                "errorMessage" => "L'adresse indiqué n'existe pas"
            ]);
        }

        if ($data['city'] !== null){
            $adresse->setCity($data['city']);
        }
        if ($data['rue'] !== null){
            $adresse->setRue($data['rue']);
        }
        if ($data['num_rue'] !== null){
            $adresse->setNumRue($data['num_rue']);
        }
        if ($data['cpm_adresse'] !== null){
            $adresse->setComplementAdresse($data['cpm_adresse']);
        }
        if ($data['name'] !== null){
            $adresse->setName($data['name']);
        }
        if ($data['city'] !== null){
            $adresse->setCity($data['city']);
        }
        if ($data['postalCode'] !== null){
            $adresse->setPostalCode($data['postalCode']);
        }

        $adresseRepository->save($adresse,true);

        return new JsonResponse();

    }

}

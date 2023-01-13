<?php

namespace App\Controller;

use App\Entity\User;
use App\GlobalFunction\FunctionErrors;
use App\Repository\UserRepository;
use ContainerCiO9nmx\getAdresseRepositoryService;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function PHPSTORM_META\map;

#[Route('api/user')]
class UserController extends AbstractController
{
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->encoder = $passwordHasher;
    }

    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route(
        '/register/{name}/{surname}/{email}/{password}/{passwordConfirm}',
        name: 'user_register', methods: ["POST"])]
    public function register(UserRepository $userRepository, Request $request, ValidatorInterface $validator
    ): JsonResponse
    {

        /* Récupération des attributs dans la requètes POST en les settant à la nouvelle entitée User*/
        $user = new User();
        $user->setName($request->attributes->get('name'));
        $user->setSurname($request->attributes->get('surname'));
        $user->setEmail($request->attributes->get('email'));

        $password = $request->attributes->get('password');
        $passwordConfirm = $request->attributes->get('passwordConfirm');
        $user->setPassword($password);



        /* Gestion des erreurs avec ValidatorInterface qui utilise les annotations Assets exemple #[Assert\Email(message: "L'email n'est pas valide.")]*/
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString,400);
        }

        if ($password === $passwordConfirm) {
            $user->setPassword($this->encoder->hashPassword($user, $user->getPassword()));
        }else {
            return new JsonResponse(["error" => "les mots de passe ne sont pas idendiques"],400);
        }

        if ($userRepository->findUserByMail($user->getEmail()) != null){
            return new JsonResponse([
                "errorCode" => "001",
                "errorMessage" => "L'adresse email est déjà inscrite dans la base de données"
                ],409);
        } ;

        $userRepository->save($user,true);


        return new JsonResponse(null,200);
    }

    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/getUserByMail/{email}', name: 'user_getByMail', methods: "GET")]
    public function getUserByMail(UserRepository $userRepository, Request $request): JsonResponse
    {
        $user = $userRepository->findOneBy(array('email' => $request->attributes->get('email')));
        $userArray = [
        'id' => $user->getId(),
        'name' => $user->getName(),
        'surname' => $user->getSurname(),
        ];
        return new JsonResponse($userArray);
    }

    /**
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
     #[Route('/', name: 'user_all', methods:"GET")]
     public function findUser(UserRepository $userRepository): JsonResponse
     {
         $users = $userRepository->findAll();
         $userArray = [];
         foreach($users as $user){

            $inCommande = false;
            foreach ($user->getCommandes() as $userCommande) {
                if ($userCommande->getEtatCommande() === "En préparation") {
                    $inCommande = true;
                }elseif ($userCommande->getEtatCommande() === "En cours de livraison") {
                    /*Si l'utilisateur à une commande en cours l'erreur 14 est retourné */
                    $inCommande = true;
                }
            }

            $jsonProduct = [
                 'id' => $user->getId(),
                 'name' => $user->getName(),
                 'surname' => $user->getSurname(),
                 'email' => $user->getEmail(),
                 'roles' => $user->getRoles(),
                 'phone' => $user->getPhone(),
                 'created_at' => $user->getCreatedAt()->format("d-m-Y"),
                 'inCommande' => $inCommande,
                 'disable' => $user->getDisable(),
             ];
             $userArray[] = $jsonProduct;
     }
     return new JsonResponse($userArray);
    }

    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route(
        '/create/{name}/{surname}/{email}/{password}/{passwordConfirm}/{roles}/{phone}',
        name: 'user_create', methods: ["POST"]
    )]
    public function create(
        UserRepository $userRepository, Request $request, ValidatorInterface $validator): JsonResponse
    {

        $role = $request->attributes->get('roles');
        /* Récupération des attributs dans la requètes POST en les settant à la nouvelle entitée User*/
        $user = new User();
        $user->setName($request->attributes->get('name'));
        $user->setSurname($request->attributes->get('surname'));
        $user->setEmail($request->attributes->get('email'));

        if ($request->attributes->get('phone') !== "-"){
            $user->setPhone($request->attributes->get('phone'));
        }

        $password = $request->attributes->get('password');
        $passwordConfirm = $request->attributes->get('passwordConfirm');
        $user->setPassword($password);

        if ($role !== 'ROLE_USER' && $role !== 'ROLE_ADMIN') {
            return new JsonResponse([
                "errorCode" => "008",
                "errorMessage" => "Le Role n'existe pas"
                ], 409);
        } else {
            $user->setRoles(array($role));
        }
        
        /* Gestion des erreurs avec ValidatorInterface qui utilise les annotations Assets exemple #[Assert\Email(message: "L'email n'est pas valide.")]*/
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString,400);
        }

        if ($password === $passwordConfirm) {
            $user->setPassword($this->encoder->hashPassword($user, $user->getPassword()));
        }else {
            return new JsonResponse(["error" => "les mots de passe ne sont pas idendiques"],400);
        }

        if ($userRepository->findUserByMail($user->getEmail()) != null){
            return new JsonResponse([
                "errorCode" => "001",
                "errorMessage" => "L'adresse email est déjà inscrite dans la base de données"
                ],409);
        }

        $userRepository->save($user,true);

        $userArray = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "surname" => $user->getSurname()
        ];

        return new JsonResponse($userArray,200);
    }

    /**
     * @param UserRepository $userRepository
     * @param FunctionErrors $errorsCodes
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/delete/{id}', name: 'user_delete', methods:"PUT")]
    public function deleteUser(UserRepository $userRepository, FunctionErrors $errorsCodes, Request $request
    ): JsonResponse
    {
        $user = $userRepository->findOneById($request->attributes->get('id'));

        if($user->getRoles()[0] == "ROLE_ADMIN") {
            return new JsonResponse([
                "errorCode" => "016",
                "errorMessage" => "Un administrateur ne peut pas être supprimé"
                ], 406);
        } else {
            $user->setName("Anonymous");
            $user->setSurname("Anonymous");
            $user->setEmail("Anonymous".$request->attributes->get('id'));
            $user->setPhone("Anonymous");
            foreach ($user->getAdresse() as $userAdresse) {
                $userAdresse->setRue("Anonymous");
            }
            foreach ($user->getCommandes() as $userCommande) {
                if ($userCommande->getEtatCommande() === "En préparation") {
                    return $errorsCodes->generateCodeError015();
                }elseif ($userCommande->getEtatCommande() === "En cours de livraison") {
                    /*Si l'utilisateur à une commande en cours l'erreur 14 est retourné */
                    return $errorsCodes->generateCodeError014();
                }
            }
    
            if (!$user) {
                return new JsonResponse([
                    "errorCode" => "009",
                    "errorMessage" => "L'utilisateur n'existe pas"
                ], 404);
            }else {   
                foreach ($user->getCommandes() as $userCommande) {
                    $userCommande->setRue(null);
                    $userCommande->setNumRue(null);
                }
                $user->setDisable(true);
                $userRepository -> save($user, true);
            }
            
            $userArray[] = [
                "id" => $user->getId(),
                "name" => $user->getName(),
                "surname" => $user->getSurname(),
            ];
            return new JsonResponse($userArray, 200);
        }
    }

    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route(
        '/update/{id}/{name}/{surname}/{email}/{password}/{passwordConfirm}/{roles}/{phone}',
        name: 'user_update', methods:"PUT")]
    public function updateUser(
        UserRepository $userRepository, Request $request, ValidatorInterface $validator
    ): JsonResponse
    {
        $role = $request->attributes->get('roles');
        $user = $userRepository->find($request->attributes->get('id'));
        if ($user === null) {
            return new JsonResponse([
                "errorMessage" => "This user don't exist "
            ], 409);
        }

        if ($request->attributes->get('name') !== "-"){
            $user->setName($request->attributes->get('name'));
        }

        if ($request->attributes->get('surname') !== "-"){
            $user->setSurname($request->attributes->get('surname'));
        }

        if ($request->attributes->get('email') !== "-"){
            $user->setEmail($request->attributes->get('email'));
        }

        if ($request->attributes->get('phone') !== "-"){
            $user->setPhone($request->attributes->get('phone'));
        }else{
            $user->setPhone(null);
        }

        if ($request->attributes->get('password') !== "-"){
            $password = $request->attributes->get('password');
            $passwordConfirm = $request->attributes->get('passwordConfirm');
            $user->setPassword($password);

            if ($password === $passwordConfirm) {
                $user->setPassword($this->encoder->hashPassword($user, $user->getPassword()));
            }else {
                return new JsonResponse(["error" => "les mots de passe ne sont pas idendiques"],400);
            }
        }

        if ($role !== "-") {
            if ($role !== 'ROLE_USER' && $role !== 'ROLE_ADMIN'){
                return new JsonResponse([
                    "errorCode" => "008",
                    "errorMessage" => "Le Role n'existe pas"
                ], 409);
            }else {
                $user->setRoles(array($role));
            }
        }


        /* Gestion des erreurs avec ValidatorInterface qui utilise les annotations Assets exemple #[Assert\Email(message: "L'email n'est pas valide.")]*/
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString,400);
        }

        $userRepository->save($user,true);

        $userArray = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "surname" => $user->getSurname(),
        ];
        return new JsonResponse($userArray,200);
    }

    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/getDisableById/{id}', name: 'user_getDisableById', methods: "GET")]
    public function getDisableById(UserRepository $userRepository, Request $request): JsonResponse
    {
        $user = $userRepository->findOneBy(array('id' => $request->attributes->get('id')));
        $userArray = [
            'disable' => $user->getDisable()
        ];
        return new JsonResponse($userArray);
    }

    /**
     * @param UserRepository $userRepository
     * @param FunctionErrors $errorsCodes
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/multipleDelete', name: 'user_multiple_delete', methods:"PUT")]
    public function multipleDeleteUser(
        UserRepository $userRepository,
        FunctionErrors $errorsCodes,
        Request $request
    ): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        foreach($data as $id) {
            $user = $userRepository->findOneById($id);

            if($user->getRoles()[0] == "ROLE_ADMIN") {
                return new JsonResponse([
                    "errorCode" => "016",
                    "errorMessage" => "Un administrateur ne peut pas être supprimé"
                    ], 406);
            } else {
                $user->setName("Anonymous");
                $user->setSurname("Anonymous");
                $user->setEmail("Anonymous".$id);
                $user->setPhone("Anonymous");
                foreach ($user->getAdresse() as $userAdresse) {
                    $userAdresse->setRue("Anonymous");
                }
                foreach ($user->getCommandes() as $userCommande) {
                    if ($userCommande->getEtatCommande() === "En préparation") {
                        return $errorsCodes->generateCodeError015();
                    }elseif ($userCommande->getEtatCommande() === "En cours de livraison") {
                        /*Si l'utilisateur à une commande en cours l'erreur 14 est retourné */
                        return $errorsCodes->generateCodeError014();
                    }
                }

                if (!$user) {
                    return new JsonResponse([
                        "errorCode" => "009",
                        "errorMessage" => "L'utilisateur n'existe pas"
                    ], 404);
                }else {
                    foreach ($user->getCommandes() as $userCommande) {
                        $userCommande->setRue(null);
                        $userCommande->setNumRue(null);
                    }
                    $user->setDisable(true);
                    $userRepository -> save($user, true);
                }
            }
        }
        return new JsonResponse(null,200);

    }


    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/stats/register/{year}', name: 'app_user_stats_register', methods: ['POST'])]
    public function getUserSign(UserRepository $userRepository, Request $request):JsonResponse
    {
        $year = $request->attributes->get('year');
        $data = $userRepository->getUserSignByYear($year);
        $dateJson = [
            "Janvier" => $data[0][1],
            "Février" => $data[1][1],
            "Mars" => $data[2][1],
            "Avril" => $data[3][1],
            "Mai" => $data[4][1],
            "Juin" => $data[5][1],
            "Juillet" => $data[6][1],
            "Août" => $data[7][1],
            "Septembre" => $data[8][1],
            "Octobre" => $data[9][1],
            "Novembre" => $data[10][1],
            "Décembre" => $data[11][1],
        ];
        return new JsonResponse($dateJson);
    }

    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/stats/count', name: 'app_user_stats_count', methods: ['GET'])]
    public function countUser(UserRepository $userRepository, Request $request): JsonResponse{

        $count = $userRepository->countUser();
        return new JsonResponse([
            "countUser" => $count[0][1]
        ]);
    }
}
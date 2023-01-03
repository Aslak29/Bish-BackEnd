<?php

namespace App\Controller;

use App\Entity\User;
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
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/register/{name}/{surname}/{email}/{password}/{passwordConfirm}', name: 'user_register', methods: ["POST"])]
    public function register(UserRepository $userRepository, Request $request, ValidatorInterface $validator): JsonResponse{

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
             $jsonProduct = [
                 'id' => $user->getId(),
                 'name' => $user->getName(),
                 'surname' => $user->getSurname(),
                 'email' => $user->getEmail(),
                 'roles' => $user->getRoles(),
                 'phone' => $user->getPhone(),
                 'created_at' => $user->getCreatedAt()->format("d-m-Y"),
             ];
             $userArray[] = $jsonProduct;
     }
     return new JsonResponse($userArray);
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
    #[Route('/create/{name}/{surname}/{email}/{password}/{passwordConfirm}/{roles}/{phone}', name: 'user_create', methods: ["POST"])]
    public function create(UserRepository $userRepository, Request $request, ValidatorInterface $validator): JsonResponse{

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

        if($role !== 'ROLE_USER' && $role !== 'ROLE_ADMIN'){
            return new JsonResponse([
                "errorCode" => "008",
                "errorMessage" => "Le Role n'existe pas"
                ],409);
        }else{
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
        };

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
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/delete/{id}', name: 'user_delete', methods:"POST")]
    public function deleteUser(UserRepository $userRepository, Request $request): JsonResponse
    {
        $user = $userRepository->findOneById($request->attributes->get('id'));
        $user->setName("Anonymous");
        $user->setSurname("Anonymous");
        $user->setEmail("Anonymous".$request->attributes->get('id'));
        $user->setPhone("Anonymous");
        foreach ($user->getAdresse() as $userAdresse){
            $userAdresse->setRue("Anonymous");
        }

        if (!$user){
            return new JsonResponse([
                "errorCode" => "009",
                "errorMessage" => "L'utilisateur n'existe pas"
            ],404);
        }else{
            $userRepository -> save($user,true);
        }
        
        $userArray = [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "surname" => $user->getSurname()
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
    #[Route('/update/{id}/{name}/{surname}/{email}/{password}/{passwordConfirm}/{roles}/{phone}', name: 'user_update', methods:"POST")]
    public function updateUser(UserRepository $userRepository, Request $request,  ValidatorInterface $validator): JsonResponse
    {
        $role = $request->attributes->get('roles');
        $user = $userRepository->find($request->attributes->get('id'));
        if ($user === null){
            return new JsonResponse([
                "errorMessage" => "This user don't exist "
            ],409);
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

        if ($role !== "-"){
            if ($role !== 'ROLE_USER' && $role !== 'ROLE_ADMIN'){
                return new JsonResponse([
                    "errorCode" => "008",
                    "errorMessage" => "Le Role n'existe pas"
                ],409);
            }else{
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
}

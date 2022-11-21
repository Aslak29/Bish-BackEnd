<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/security')]
class UserController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (email="User")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    // RECUPERATION DU MOT DE PASSE PAR MAIL
    #[Route('/forgot/{email}', name: 'user_forgot', methods: ["POST"])]


    public function register(UserRepository $userRepository, Request $request, ValidatorInterface $validator): JsonResponse{
        $user = User;
        /* Récupération des attributs dans la requètes POST en les settant à la nouvelle entitée User*/
        $user->setEmail($request->attributes->get('email'));
        $newPassword = $request->attributes->get('password');
        $newPasswordConfirm = $request->attributes->get('passwordConfirm');
        $user->setPassword($newPassword);

        $this->newPassword = $password;

        return $this;

        {
        
        /* Gestion des erreurs avec ValidatorInterface qui utilise les annotations Assets exemple #[Assert\Email(message: "L'email n'est pas valide.")]*/
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString,400);
        }

        if ($newPassword === $newPasswordConfirm) {
            $user->setPassword($this->encoder->hashPassword($user, $user->getPassword()));
        }else {
            return new JsonResponse(["error" => "les mots de passe ne sont pas identiques"],400);
        }

        if ($userRepository->findUserByMail($user->getEmail()) != null){
            return new JsonResponse([
                "errorCode" => "001",
                "errorMessage" => "L'adresse email n'existe pas dans la base de données"
                ],409);
        } ;
        $userRepository->save($newPassword,true);

        return new JsonResponse(null,200);
        }
    }
}

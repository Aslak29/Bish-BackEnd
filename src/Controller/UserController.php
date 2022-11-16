<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

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
    #[Route('/register/{name}/{surname}/{email}/{password}', name: 'user_register', methods: ["POST"])]
    public function register(UserRepository $userRepository, Request $request): JsonResponse{

        $user = new User();
        $user->setName($request->attributes->get('name'));
        $user->setSurname($request->attributes->get('surname'));
        $user->setEmail($request->attributes->get('email'));
        $user->setPassword($this->encoder->hashPassword($user, $request->attributes->get('password')));

        $userRepository->save($user,true);

        return new JsonResponse(null,200);
    }

}
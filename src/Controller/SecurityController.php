<?php

namespace App\Controller;

use App\Repository\UserRepository;
use OpenApi\Annotations as OA;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('api/security')]
class SecurityController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Security")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    // RECUPERATION DU MOT DE PASSE PAR MAIL
    #[Route('/forgot/{email}', name: 'user_forgot', methods: ["POST"])]

    public function sendEmail(UserRepository $userRepository, Request $request, MailerInterface $mailer): JsonResponse
    
    {
        if($userRepository->findUserByMail($request->attributes->get('email')!= null)){

            $email = (new Email())
                ->from('hello@example.com')
                ->to($request->attributes->get('email'))
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        }else{

            return new JsonResponse([
                "errorCode" => "002",
                "errorMessage" => "This email doesn't exist, we can't send mail"
            ],409);
        }

    return new JsonResponse();
    }
}

<?php

namespace App\Controller;

use App\Repository\UserRepository;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
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
     * @param MailerInterface $mailer
     * @return JsonResponse
     * @OA\Tag (name="Security")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    // RECUPERATION DU MOT DE PASSE PAR MAIL
    #[Route('/forgot/{email}', name: 'user_forgot', methods: ["POST"])]

    public function sendEmail(UserRepository $userRepository, Request $request, MailerInterface $mailer,MessageBusInterface $messageBus)  : JsonResponse {

        if($userRepository->findUserByMail($request->attributes->get('email')) != null){

            $email = (new TemplatedEmail())
                ->from('bishincubateur@gmail.com')
                ->to($request->attributes->get('email'))
                ->subject('Demande de reinitialisation de mot de passe')
                ->text('Lien pour réinitialiser : ' )
                ->html('<h1>Hello World</h1> <p>...</p>');
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
            }
        }else{
            return new JsonResponse([
                "errorCode" => "002",
                "errorMessage" => "This email doesn't exist, we can't send mail"
            ],409);
        }

    return new JsonResponse();
    }
}

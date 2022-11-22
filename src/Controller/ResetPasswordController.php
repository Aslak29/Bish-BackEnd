<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use OpenApi\Annotations as OA;

#[Route('api/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @OA\Tag (name="ResetPassWord")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/sendMail/{email}', name: 'app_forgot_password_request',methods: ['POST'])]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        return $this->processSendingPasswordResetEmail(
                $request->attributes->get('email'),
                $mailer
            );
    }

    /**
     * Confirmation page after a user has requested a password reset.
     * @return JsonResponse
     * @OA\Tag (name="ResetPassWord")
     * @OA\Response(
     *     response="200",
     *     description = "OK")
     */
    #[Route('/check-email', name: 'app_check_email', methods: ['GET'])]
    public function checkEmail(): JsonResponse
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return new JsonResponse([
            'successCode' => '001',
            'successMessage' => 'Token Generate, stock 1 hour in BDD'
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *  @OA\Tag (name="ResetPassWord")
     *  @OA\Response(
     *  response="200",
     *  description = "OK"
     * )
     */
    #[Route('/reset/{token}/{password}/{passwordConfirm}', name: 'app_reset_password', methods: ['POST'])]
    public function reset(Request $request, TranslatorInterface $translator, string $token = null, UserPasswordHasherInterface $passwordHasher,ResetPasswordRequestRepository $resetPasswordRequestRepository): RedirectResponse|JsonResponse
    {
        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($request->attributes->get("token"));
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        $user->setPassword($passwordHasher->hashPassword($user,$request->attributes->get("password")));
        $this->entityManager->flush();

        $this->resetPasswordHelper->removeResetRequest($request->attributes->get("token"));

        return new JsonResponse([
            "successCode" => "002",
            "successMessage" => "Your password has been changed"
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailData, MailerInterface $mailer): RedirectResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email'); // Change with JsonResponse
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->redirectToRoute('app_check_email'); // Change with JsonResponse
        }

        $email = (new TemplatedEmail())
            ->from(new Address('bishincubateur@gmail.com', 'Bish'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $mailer->send($email);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}

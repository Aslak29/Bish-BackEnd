<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route('api/stripe')]
class StripeController extends AbstractController
{
    /**
     * @OA\Tag (name="Stripe")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @throws ApiErrorException
     */
    #[Route('/paymentIntent/{amount}',name: 'app_stripe_paymentIntent',methods: ['POST'])]
    public function paymentIntent(Request $request): JsonResponse {

        $amount = $request->attributes->get("amount");

        $stripe = new StripeClient('sk_test_51LwmsKBjYw0WvT4HhDKEhAOoXseKSd0B2JhUhd4eoF2NyrGyA79rOc7VfK4KvSRJLlpweRv7HHQVOsgHrP9VPRAo008FNyvIbg'); //TODO Put the secret key in .env
        $paymentIntent = $stripe->paymentIntents->create(
            ['amount' => $amount * 100, 'currency' => 'usd', 'payment_method_types' => ['card']]
        );

        return new JsonResponse([
            "id" => $paymentIntent->id
        ]);
    }

    /**
     * @OA\Tag (name="Stripe")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @throws ApiErrorException
     */
    #[Route('/clientSecret/{idPaymentIntent}',name: 'app_stripe_clientSecret',methods: ['POST'])]
    public function getClientSecret(Request $request): JsonResponse{

        $idPaymentIntent = $request->attributes->get('idPaymentIntent');

        $stripe = new StripeClient('sk_test_51LwmsKBjYw0WvT4HhDKEhAOoXseKSd0B2JhUhd4eoF2NyrGyA79rOc7VfK4KvSRJLlpweRv7HHQVOsgHrP9VPRAo008FNyvIbg'); //TODO Put the secret key in .env
        $paymentIntent = $stripe->paymentIntents->retrieve($idPaymentIntent);

        return new JsonResponse([
            "clientSecret" => $paymentIntent->client_secret
        ]);
    }


    /**
     * @OA\Tag (name="Stripe")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @throws ApiErrorException
     */
    #[Route('/PaymentMethod/{idPaymentMethod}', name:'app_stripe_paymentMethod', methods:['POST'])]
    public function paymentMethod(Request $request): JsonResponse{

        $stripe = new StripeClient('sk_test_51LwmsKBjYw0WvT4HhDKEhAOoXseKSd0B2JhUhd4eoF2NyrGyA79rOc7VfK4KvSRJLlpweRv7HHQVOsgHrP9VPRAo008FNyvIbg'); //TODO Put the secret key in .env
        $paymentMethods = $stripe->paymentMethods->retrieve($request->attributes->get('idPaymentMethod'));
        return new JsonResponse($paymentMethods,200);
    }

    /**
     * @OA\Tag (name="Stripe")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @throws ApiErrorException
     */
    #[Route('/setPaymentIntent/PaymentMethod/{idPaymentIntent}/{idPaymentMethod}', name:'app_stripe_update_PaymentIntent', methods:['POST'])]
    public function updatePaymentMethodInPaymentIntent(Request $request): JsonResponse{

        $stripe = new StripeClient('sk_test_51LwmsKBjYw0WvT4HhDKEhAOoXseKSd0B2JhUhd4eoF2NyrGyA79rOc7VfK4KvSRJLlpweRv7HHQVOsgHrP9VPRAo008FNyvIbg'); //TODO Put the secret key in .env

        $paymentIntent = $stripe->paymentIntents->update($request->attributes->get('idPaymentIntent'),[
            'payment_method' => $request->attributes->get('idPaymentMethod')
        ]);

        return new JsonResponse($paymentIntent,200);
    }



}

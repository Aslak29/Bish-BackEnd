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

    /**
     * @OA\Tag (name="Stripe")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @throws ApiErrorException
     */
    #[Route('/confirmPayment/{idPaymentIntent}', name:'app_stripe_confirm_PaymentIntent', methods:['POST'])]
    public function confirmPayment(Request $request): JsonResponse{

        $stripe = new StripeClient('sk_test_51LwmsKBjYw0WvT4HhDKEhAOoXseKSd0B2JhUhd4eoF2NyrGyA79rOc7VfK4KvSRJLlpweRv7HHQVOsgHrP9VPRAo008FNyvIbg'); //TODO Put the secret key in .env
        $paymentIntent = $stripe->paymentIntents->confirm($request->attributes->get('idPaymentIntent'));
        return new JsonResponse($paymentIntent,200);
    }


    /**
     * @OA\Tag (name="Stripe")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @throws ApiErrorException
     */
    #[Route('/setPaymentIntent/amount/{idPaymentIntent}/{amount}', name: 'app_stripe_amount_update', methods:['POST'])]
    public function updateAmount(Request $request): JsonResponse{

        $stripe = new StripeClient('sk_test_51LwmsKBjYw0WvT4HhDKEhAOoXseKSd0B2JhUhd4eoF2NyrGyA79rOc7VfK4KvSRJLlpweRv7HHQVOsgHrP9VPRAo008FNyvIbg'); //TODO Put the secret key in .env
        $paymentIntent = $stripe->paymentIntents->retrieve($request->attributes->get("idPaymentIntent"));
        $paymentMethods = null;
        
        if($paymentIntent->payment_method) {
            $paymentMethods = $stripe->paymentMethods->retrieve($paymentIntent->payment_method);
        }

        $epsilon = 0.0000001;
        if (abs($paymentIntent->amount - $request->attributes->get("amount") * 100) > $epsilon){
            $paymentIntent = $stripe->paymentIntents->update($request->attributes->get("idPaymentIntent"),
                [
                    "amount" => $request->attributes->get("amount") * 100
                ]);
            return new JsonResponse([$paymentIntent, $paymentMethods]);
        }

        return new JsonResponse(["La valeur a update est la même", $paymentMethods]);
    }

    /**
     * @OA\Tag (name="Stripe")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     * @throws ApiErrorException
     */
    #[Route('/cancelPaymentIntent/{idPaymentIntent}', name: 'app_stripe_paymentIntent_cancel', methods:['POST'])]
    public function cancelPaymentIntent(Request $request): JsonResponse{

        $stripe = new StripeClient('sk_test_51LwmsKBjYw0WvT4HhDKEhAOoXseKSd0B2JhUhd4eoF2NyrGyA79rOc7VfK4KvSRJLlpweRv7HHQVOsgHrP9VPRAo008FNyvIbg'); //TODO Put the secret key in .env
        $paymentIntent =  $stripe->paymentIntents->cancel($request->attributes->get("idPaymentIntent"),[]);

        return new JsonResponse($paymentIntent,200);
    }


}

<?php
namespace App\Controller;

use App\Entity\Promotions;
use App\Repository\ProduitRepository;
use App\Repository\PromotionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/api/promotion')]
class PromotionController extends AbstractController
{
    /**
     * @param PromotionsRepository $promotionRepository
     * @return JsonResponse
     * @OA\Tag (name="Promotion")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/', name: 'app_promotion', methods: ['GET'])]
    public function index(PromotionsRepository $promotionRepository): JsonResponse
    {
        $promotions = $promotionRepository->findAll();
        $promoArray = [];

        foreach ($promotions as $promotion) {
            $promoArray[] = [
                'id' => $promotion->getId(),
                'remise' => $promotion->getRemise(),
                'start_date' => $promotion->getDateStart()->format("d-m-Y H:i:s"),
                'end_date' => $promotion->getDateEnd()->format("d-m-Y H:i:s")
            ];
        }
        return new JsonResponse($promoArray, 200);
    }

    /**
     * @param PromotionsRepository $promotionsRepository
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     * @OA\Tag (name="Promotion")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/add/{remise}/{startdate}/{enddate}', name: 'app_promotion_add', methods: ['POST'])]
    public function addPromotion(PromotionsRepository $promotionsRepository, Request $request, ValidatorInterface $validator): JsonResponse
    {

        $startDate = new \DateTime($request->attributes->get('startdate'));
        $endDate = new \DateTime($request->attributes->get('enddate'));

        if ($endDate < $startDate) {
            return new JsonResponse([
                "errorCode" => "A définir",
                "errorMessage" => "End date cannot be inferior at start date"
            ]);
        } else {
            $newPromotion = new Promotions();
            $newPromotion->setRemise($request->attributes->get('remise'));
            $newPromotion->setDateStart($startDate);
            $newPromotion->setDateEnd($endDate);
            $errors = $validator->validate($newPromotion);

            if (count($errors) > 0) {
                $errorsString = (string)$errors;
                return new JsonResponse($errorsString);
            }
        }
        $promotionsRepository->save($newPromotion, true);

        return new JsonResponse([
            "successCode" => "A définir",
            "successMessage" => "This promotion has been add"
        ]);
    }

    /**
     * @param PromotionsRepository $promotionsRepository
     * @param ProduitRepository $produitRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Promotion")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/remove/{id}', name: 'app_promotion_remove', methods: ['DELETE'])]
    public function removePromotion(PromotionsRepository $promotionsRepository, ProduitRepository $produitRepository, Request $request): JsonResponse
    {

        $promotion = $promotionsRepository->find($request->attributes->get('id'));
        $produitsByPromo = $produitRepository->findProductByIdPromo($request->attributes->get('id'));

        if ($promotion === null) {
            return new JsonResponse([
                "errorCode" => "A définir",
                "errorMessage" => "This promotions dont exist"
            ]);
        } else {
            foreach ($produitsByPromo as $produit) {
                $produit->setPromotions(null);
            }
            $promotionsRepository->remove($promotion, true);
            return new JsonResponse([
                "This promotion has been remove"
            ]);
        }
    }

    /**
     * @param PromotionsRepository $promotionsRepository
     * @param ProduitRepository $produitRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Promotion")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/update/{id}/{remise}/{datestart}/{dateend}', name: 'app_promotion_update', methods: ['POST'])]
    public function updatePromotion(PromotionsRepository $promotionsRepository, Request $request): JsonResponse {

        $promotionUpdate = $promotionsRepository->find($request->attributes->get('id'));

        if ($promotionUpdate === null){
            return new JsonResponse([
                "errorCode" => "A définir",
                "errorMessage" => "This promotions dont exist"
            ]);
        } else {
            if ($request->attributes->get('remise') !== "-"){
                $promotionUpdate->setRemise($request->attributes->get('remise'));
            }
            if ($request->attributes->get('datestart') !== "-"){
                $promotionUpdate->setDateStart(new \DateTime($request->attributes->get('datestart')));
            }
            if ($request->attributes->get('dateend') !== "-"){
                $promotionUpdate->setDateEnd(new \DateTime($request->attributes->get('dateend')));
            }
            $promotionsRepository->save($promotionUpdate,true);
            return new JsonResponse([
                "sucessCode" => "A définir",
                "sucessMessage" => "This promotions has been update"
            ]);
        }
    }
}

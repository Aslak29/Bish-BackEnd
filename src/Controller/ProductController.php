<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\StringType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// exporter vers AdminProductView ? - Flo
#[Route('api/produit')]
class ProductController extends AbstractController
{
        /**
     * @param ProduitRepository $produitRepository
     * @return JsonResponse
     * @OA\Tag (name="Produit")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */

    #[Route('/', name: 'app_produit', methods:"GET")]
    public function findProduct(ProduitRepository $produitRepository): JsonResponse
    {
        $produits = $produitRepository->findAll();
        $produitArray = [];
        foreach($produits as $produit){
            $produitArray[] = [
                'id' => $produit->getId(),
                'name' => $produit->getName(),
                'description' => $produit->getDescription(),
                'pathImage' => $produit->getPathImage(),
                'price' => $produit->getPrice(),
                'is_trend' => $produit->isIsTrend(),
                'is_available' => $produit->isIsAvailable()
            ];
        }
        return new JsonResponse($produitArray);
    }

    #[Route('/', name: 'app_add_product', methods: "POST")]
    public function addProduit(FormBuilderInterface $addForm){
        $addForm
            ->add('nom', StringType::class)
            ->add('description', TextareaType::class)
            ->add('pathImage', StringType::class)
            ->add('price', NumberType::class)
            ->add('is_trend', BooleanType::class)
            ->add('is_available', BooleanType::class)
            ->add('save', SubmitType::class)
            ;
    }
    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
            'data_class'=>Produit::class,
        ]);
    }
    public function index() : Response
    {
        $produit = new Produit();
        $form = $this->createForm(Produit::class, $produit);

        return $this->render('default/index.html.twig', [
            'controller_name' => 'ProductController',
            'form' => $form->createView()
        ]);
    }
}

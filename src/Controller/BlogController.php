<?php

namespace App\Controller;

use App\Repository\BlogRepository;
use phpDocumentor\Reflection\DocBlock\Description;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route('api/blog')]
class BlogController extends AbstractController
{
    /**
     * @param BlogRepository $blogRepository
     * @return JsonResponse
     * @OA\Tag (name="Blog")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/', name: 'app_blog', methods: "GET")]
    public function index(BlogRepository $blogRepository): JsonResponse
    {
        $blogs = $blogRepository->findAll();
        $blogArray = [];

        foreach ($blogs as $blog ){
            $blogArray[] = [
                'id' => $blog->getId(),
                'title' => $blog->getTitle(),
                'description' => $blog->getDescription(),
                'date' => $blog->getDate(),
                'pathImage' => $blog->getPathImage()
            ];
        }

        return new JsonResponse($blogArray);
    }
}

<?php

namespace App\Controller;

use App\Repository\BlogRepository;
use phpDocumentor\Reflection\DocBlock\Description;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @param BlogRepository $blogRepository
     * @return JsonResponse
     * @OA\Tag (name="Blog")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/{limit}/{offset}', name: 'app_blog_pagination', methods: "POST")]
    public function blogPagination(BlogRepository $blogRepository, Request $request): JsonResponse
    {
        $blogs = $blogRepository->findArticlesLimit($request->attributes->get("limit"), $request->attributes->get("offset"));
        $count= $blogRepository->countBlog();
        $blogArray = [];
        $blogRes=[];

        foreach ($blogs as $blog ){
            $blogArray[] = [
                'id' => $blog->getId(),
                'title' => $blog->getTitle(),
                'description' => $blog->getDescription(),
                'date' => $blog->getDate()->format('d-m-Y'),
                'pathImage' => $blog->getPathImage(),
            ];
        };
        array_push($blogRes, $blogArray, $count);
        return new JsonResponse($blogRes);
    }


    /**
     * @param BlogRepository $blogRepository
     * @return JsonResponse
     * @OA\Tag (name="Blog")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/lastArticle', name: 'app_blog_last-article', methods: "GET")]
    public function findLastArticle(BlogRepository $blogRepository): JsonResponse
    {
        $blog = $blogRepository->findLastArticle();
        $blogArray[] = [
            "id" => $blog[0]->getId(),
            "title"=>$blog[0]->getTitle(),
            "description"=>$blog[0]->getDescription(),
            "date"=>$blog[0]->getDate()->format('d-m-Y'),
            "path_image"=>$blog[0]->getPathImage(),
        ];
        return new JsonResponse($blogArray);
    }

    /**
     * @param BlogRepository $blogRepository
     * @return JsonResponse
     * @OA\Tag (name="Blog")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/{articleID}', name: 'app_blog_by_id', methods: "GET")]
    public function findById(BlogRepository $blogRepository, Request $request): JsonResponse
    {
        $blog = $blogRepository->findOneBy(array('id' => $request->attributes->get('articleID')));
        $blogArray[] = [
            "id" => $blog->getId(),
            "title"=>$blog->getTitle(),
            "description"=>$blog->getDescription(),
            "date"=>$blog->getDate()->format('d-m-Y'),
            "path_image"=>$blog->getPathImage(),
        ];
        return new JsonResponse($blogArray);
    }
}

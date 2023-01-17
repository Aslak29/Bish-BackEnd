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
use App\Entity\Blog;

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
     * @param Request $request
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
        $blogs = $blogRepository->findArticlesLimit(
            $request->attributes->get("limit"),
            $request->attributes->get("offset")
        );
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
        }
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
        if (count($blog) > 0) {
            $blogArray[] = [
                "id" => $blog[0]->getId(),
                "title"=>$blog[0]->getTitle(),
                "description"=>$blog[0]->getDescription(),
                "date"=>$blog[0]->getDate()->format('d-m-Y'),
                "path_image"=>$blog[0]->getPathImage(),
            ];
            return new JsonResponse($blogArray);
        } else {
            return new JsonResponse([null]);
        }
    }

    /**
     * @param BlogRepository $blogRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Blog")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/find/{articleID}', name: 'app_blog_by_id', methods: "GET")]
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

// Ajouter un article de blog
/**
     * @param BlogRepository $blogRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Blog")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/add', name: 'app_add_blog', methods: "POST")]
    public function addBlog(BlogRepository $blogRepository, Request $request):JsonResponse
    {
        $blog = new Blog();        
        $data = json_decode($request->getContent(), true);

        /* Donnation des valeurs aux attributs de l'article */
        $blog->setTitle($data['title']);
        $blog->setDescription($data['description']);
        $blog->setPathImage($data['pathImage']);

        /* Insertion en bdd pour l'article de blog */
        $blogRepository->save($blog,true);

        return new JsonResponse($data, 200);
    }

// Update un article de blog

/**
     * @param BlogRepository $blogRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Blog")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/update',
        name: 'app_update_blog', methods: "PUT")]
    public function updateBlog(BlogRepository $blogRepository, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $blog = $blogRepository->find($data['id']);

        $blog->setTitle($data['title']);
        $blog->setDescription($data['description']);
        $blog->setPathImage($data['pathImage']);

        $blogRepository->save($blog, true);

        $blogArray = [
            "id" => $blog->getId(),
            "title" => $blog->getTitle(),
            "description" => $blog->getDescription(),
            "date" => $blog->getDate(),
            "pathImage" => $blog->getPathImage(),
        ];

        return new JsonResponse($blogArray, 200);
    }
// Supprimer un article de blog

    /**
     * @param BlogRepository $blogRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Blog")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/remove/{id}', name: 'app_delete_blog', methods: ['DELETE'])]
    public function deleteBlog(BlogRepository $blogRepository, Request $request):JsonResponse
    {
        $deleteBlog = $blogRepository->find($request->attributes->get('id'));
        if($deleteBlog != null){
            $blogRepository->remove($deleteBlog,true);
        }else{
            return new JsonResponse([
                'errorCode' => "013",
                'errorMessage' => "Cet article de blog n'existe pas"
            ],409);
        }
        return new JsonResponse([
            'successCode' => "004",
            'successMessage' => "Cet article de blog  été supprimé"
        ],200);
    }

    /**
     * @param BlogRepository $blogRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Blog")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/multipleRemove', name: 'app_multiple_delete_blog', methods: ['DELETE'])]
    public function multipleDeleteBlog(BlogRepository $blogRepository, Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach($data as $id) {
            $deleteBlog = $blogRepository->find($id);
            if($deleteBlog != null){
                $blogRepository->remove($deleteBlog,true);
            }else{
                return new JsonResponse([
                    'errorCode' => "013",
                    'errorMessage' => "Cet article de blog n'existe pas"
                ],409);
            }
        }
        return new JsonResponse(null,200);

    }

    /**
     * @param BlogRepository $blogRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Blog")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/count', name: 'blog_count', methods: "GET")]
    public function countBlog(BlogRepository $blogRepository):JsonResponse{

        $countBlog = $blogRepository->countAll();
        return new JsonResponse($countBlog[0]);

    }

}

<?php

namespace App\Controller;


use App\Entity\Post;
use App\Service\Minio;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

final class PostController extends AbstractController
{


    #[Route('/api/post', name: 'app_post', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {   
    
        // $user = 1;
        // $posts = $postRepository->findBy(['user_id' => $user]);
        $posts = $postRepository->findAll();
        // $posts = $postRepository->findBy(['user_id' => $user]);
        $postArray = [];
        foreach ($posts as $post) {
            $postArray[] =[
                'id'=> $post->getId(),
                'author'=> $post->getUserId(),
                'description' => $post->getDescription(),
                'image' => $post->getImageId()
            ];
        }
        
        return $this->json($postArray, 200);
    }

    #[Route(path:'/api/post', name: 'app_createPost', methods:["POST"])]
    public function createPost(HttpFoundationRequest $request, 
    EntityManagerInterface $entityManager,
    Minio $minio): JsonResponse 
    {
        
        $file = $request->files->get("image");
        $userId = $request->request->get("user_id");
        $description = $request->request->get("description");

        if (!$file) { 
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }
        
        $imageId = $minio->uploadFile($file);



        $NewPost = new Post();
        $NewPost 
            ->setUserId(1)
            ->setDescription($description)
            ->setImageId($imageId);

        $entityManager->persist($NewPost);
        $entityManager->flush();

        return new JsonResponse(
            ["message" => "Post crée"],
            Response::HTTP_OK
        );
    }
}

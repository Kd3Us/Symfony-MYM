<?php

namespace App\Controller;

use App\Entity\Like;
use App\Repository\LikeRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LikeController extends AbstractController
{
    #[Route('/api/posts/{id}/likes', name: 'get_post_likes', methods: ['GET'])]
    public function getPostLikes(int $id, LikeRepository $likeRepository): JsonResponse
    {
        $likes = $likeRepository->findBy(['postId' => $id]);
        $likesData = array_map(function ($like) {
            return [
                'id' => $like->getId(),
                'userId' => $like->getUserId(),
                'createdAt' => $like->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $likes);

        return $this->json($likesData);
    }

    #[Route('/api/posts/{id}/like', name: 'like_post', methods: ['POST'])]
    public function likePost(int $id, PostRepository $postRepository, LikeRepository $likeRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user || !isset($user->id)) {
            return new JsonResponse(
                ["error" => "You are not connected"],
                Response::HTTP_UNAUTHORIZED
            );
        }
        $user = $userRepository->find($user->id);

        if (!$user || !isset($user)) {
            return new JsonResponse(
                ["error" => "User not found"],
                Response::HTTP_NOT_FOUND
            );
        }

        $post = $postRepository->find($id);

        if (!$post) {
            return new JsonResponse(
                ["error" => "Post not found"],
                Response::HTTP_NOT_FOUND
            );
        }

        $like = $likeRepository->findLikeByPostAndUser($post->getId(), $user->getId());
        if (isset($like)) {
            return new JsonResponse(
                ["error" => "Like already exists"],
                Response::HTTP_CONFLICT
            );
        }

        $like = new Like();
        $like
            ->setPost($post)
            ->SetUser($user)
            ->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($like);
        $entityManager->flush();

        return new JsonResponse(
            ["message" => "Post liked successfully"],
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/posts/{id}/unlike', name: 'unlike_post', methods: ['DELETE'])]
    public function unlikePost(int $id, LikeRepository $likeRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if (!$user || !isset($user->id)) {
            return new JsonResponse(
                ["error" => "You are not connected"],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $like = $likeRepository->findOneBy(['postId' => $id, 'userId' => $user->id]);

        if (!$like) {
            return new JsonResponse(
                ["error" => "Like not found"],
                Response::HTTP_NOT_FOUND
            );
        }

        $entityManager->remove($like);
        $entityManager->flush();

        return new JsonResponse(
            ["message" => "Like removed successfully"],
            Response::HTTP_OK
        );
    }
}

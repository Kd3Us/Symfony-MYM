<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Repository\FollowRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FollowController extends AbstractController
{
    #[Route('/api/users/{id}/follow', name: 'follow_user', methods: ['POST'])]
    public function followUser(int $id  , UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {

        $user = $this->getUser();

        if (!$user || !isset($user->id)) {
            return new JsonResponse(
                ["error" => "You are not connected"],
                Response::HTTP_NOT_FOUND
            );
        }

        $authorUser = $userRepository->find($user->id);

        $targetUser = $userRepository->find($id);

        if (!$targetUser) {
            return new JsonResponse(
                ["error" => "User not found"],
                Response::HTTP_NOT_FOUND
            );
        }

        $user = new Follow();
        $user
            ->setFollowed($targetUser)
            ->setFollower($authorUser)
            ->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            "message" => "User followed successfully",
        ], Response::HTTP_CREATED);

        // return new JsonResponse($user->jsonSerialize(), Response::HTTP_CREATED);
    }
    
    #[Route('/api/users/{id}/followers', name: 'get_user_followers', methods: ['GET'])]
    public function getFollowers(int $id, FollowRepository $followRepository, UserRepository $userRepository) : JsonResponse
    {
        $followers = $followRepository->findFollowersByUserId($id);
        $followersData = array_map(function ($follower) use ($userRepository) {
            $followerEntity = $userRepository->find($follower->getId());
            return [
                'id' => $followerEntity->getId(),
                'username' => $followerEntity->getUsername(),
                'email' => $followerEntity->getEmail(),
            ];
        }, $followers);

        return $this->json($followersData);
    }
    
    #[Route('/api/users/{id}/following', name: 'get_user_following', methods: ['GET'])]
    public function getFollowing(int $id, FollowRepository $followRepository, UserRepository $userRepository): Response
    {
        $following = $followRepository->findFollowingByUserId($id);
        $followingData = array_map(function ($followed) use ($userRepository) {
            $followed = $userRepository->find($followed->getId());
            return [
                'id' => $followed->getId(),
                'username' => $followed->getUsername(),
                'email' => $followed->getEmail(),
            ];
        }, $following);

        return $this->json($followingData);
    }

    #[Route('/api/users/{id}/unfollow', name: 'unfollow_user', methods: ['DELETE'])]
    public function unfollowUser(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(
                ["error" => "User not found"],
                Response::HTTP_NOT_FOUND
            );
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(
            ["message" => "User deleted successfully"],
            Response::HTTP_OK
        );
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    // Route for GET and POST requests
    #[Route('api/profile', name: 'profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        //  authenticated user
        $user = $this->getUser();

        // If the user is not authenticated, deny access
        if (!$user) {
            throw new AccessDeniedException('Access denied: User not authenticated.');
        }

        // If the method is GET, return the user's profile
        if ($request->isMethod('GET')) {
            return $this->json([
                'status' => 'success',
                'user' => [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                ]
            ]);
        }

        // If the method is POST, update the user's profile
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);

            // update mail
            if (isset($data['email'])) {
                $user->setEmail($data['email']);
            }

            // update username
            if (isset($data['username'])) {
                $user->setUsername($data['username']);
            }

            // persist changes to the database
            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'user' => [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                ]
            ]);
        }

        // sinon...
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Invalid method'
        ], Response::HTTP_METHOD_NOT_ALLOWED);
    }
}

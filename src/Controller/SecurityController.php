<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityController extends AbstractController
{
    #[Route('api/login', name: 'app_login', methods: ['POST'])]
    public function login(AuthenticationUtils $authenticationUtils): JsonResponse
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Get the last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Authentication failed',
                'error' => $error->getMessage()
            ], 401);
        }

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Login successful',
            'last_username' => $lastUsername
        ], 200);
    }

    #[Route('api/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        // Logout is handled by Symfony's security system.
        throw new \LogicException('This method can be empty - it will be intercepted by the logout key on your firewall.');
    }
}

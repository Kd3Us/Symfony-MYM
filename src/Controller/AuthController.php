<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;  // alias

final class AuthController extends AbstractController
{
    #[Route('api/auth/login', name: 'app_login', methods: ['POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($error) {
            return $this->json([
                'status' => 'error',
                'message' => 'Authentication failed',
                'error' => $error->getMessage(),
            ], 401);
        }

        return $this->json([
            'status' => 'success',
            'message' => 'Login success',
            'last_username' => $lastUsername,
        ], 200);
    }

    #[Route('api/auth/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        SymfonyValidatorInterface $validator  // alias
    ): Response
    {

        $data = json_decode($request->getContent(), true);


        if (!isset($data['username'], $data['email'], $data['password'])) {
            return $this->json([
                'status' => 'error',
                'message' => 'Missing fields: username, email, or password',
            ], 400);  // Bad request
        }

        // Create a new User entity
        $user = new User();
        $user->setUserName($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);  // unhashed password

        // Manually validate the user entity
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            // validation errors
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return $this->json([
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $errorMessages,
            ], 400);
        }

        // hashing  password
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Set role
        $user->setRoles(['ROLE_USER']);

        // save user in database
        $entityManager->persist($user);
        $entityManager->flush();

        // return success response
        return $this->json([
            'status' => 'success',
            'message' => 'User created successfully.',
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUserName(),
                'email' => $user->getEmail(),
            ]
        ], 201);
    }

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \LogicException('This method can be empty - it will be intercepted by the logout key on your firewall.');
    }
}

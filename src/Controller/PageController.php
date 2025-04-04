<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController{
    #[Route('/', name: 'feed_page')]
    public function feed(UserRepository $userRepository, PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        $users = [];
        foreach ($posts as $post) {
            $userId = $post->getUserId();
            if (!array_key_exists($userId, $users)) {
            $users[$userId] = $userRepository->findById($userId);
            }
        }

        return $this->render('page/feed.html.twig', [
            'posts' => $posts,
            'users'=> $users
        ]);
    }


    #[Route('/login', name: 'login_page')]
    public function login(): Response
    {
        return $this->render('page/login.html.twig');
    }


    #[Route('/register', name: 'register_page')]
    public function register(): Response
    {
        return $this->render('page/register.html.twig');
    }


    #[Route('/welcome', name: 'welcome_page')]
    public function welcome(): Response
    {
        return $this->render('page/welcome.html.twig');
    }


    #[Route('/profile/{id}', name: 'profile_page')]
    public function profile($id): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }


    #[Route('/post/{id}', name: 'post_page')]
    public function post($id): Response
    {
        return $this->render('page/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
}

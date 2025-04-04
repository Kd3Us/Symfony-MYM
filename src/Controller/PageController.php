<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController{
    #[Route('/', name: 'feed_page')]
    public function feed(PostRepository $postRepository): Response
    {
        // $user = $this->getUser();
        // if (!$user) {
        //     return $this->redirectToRoute('login_page');
        // }
        
        // $posts = $postRepository->getPosts();
        $posts = [
            [
                'id'=> 1,
                'title'=> 'Post 1',
                'image'=> 'image1.jpg',
                'author' => 'John Doe',
                'date' => '2023-10-01',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'likes' => 10,
                'comments' => 5
            ],
            [
                'id'=> 2,
                'title'=> 'Post 2',
                'image'=> 'image2.jpg',
                'author' => 'Jane Smith',
                'date' => '2023-10-02',
                'content' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'likes' => 20,
                'comments' => 8
            ],
            [
                'id'=> 3,
                'title'=> 'Post 3',
                'image'=> 'image3.jpg',
                'author' => 'Alice Johnson',
                'date' => '2023-10-03',
                'content' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
                'likes' => 15,
                'comments' => 3
            ],
            [
                'id'=> 4,
                'title'=> 'Post 4',
                'image'=> 'image4.jpg',
                'author' => 'Bob Brown',
                'date' => '2023-10-04',
                'content' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'likes' => 25,
                'comments' => 12
            ]
        ];

        return $this->render('page/feed.html.twig', [
            'posts' => $posts,
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

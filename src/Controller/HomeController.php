<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    
    #[Route('/', name: 'homepage')]
    public function index(PostRepository $postRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $posts = $paginator->paginate(
            $postRepository->findAll(),
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/user/{id}', name: 'app_blog_show_by_user')]
    public function categoryIndex(User $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $posts = $paginator->paginate(
            $userRepository->getPosts(),
            $request->query->getInt('page', 1),
            3
        );
        
        return $this->render('home/index.html.twig', [
            'posts' => $posts ,
        ]);
    }
}

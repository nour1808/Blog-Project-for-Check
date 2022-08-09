<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class BlogController extends AbstractController
{
    #[Route('/blog/{id}', name: 'app_blog_show')]
    public function show(Post $post, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $comment->setPost($post);
            $manager->persist($comment);
            $manager->flush();
        }

        return $this->render('blog/show.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/blog/delete/{id}', name: 'app_blog_deleteComment')]
    public function deleteComment(Comment $comment, EntityManagerInterface $manager): Response
    {
        $postId = $comment->getPost()->getId();
        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute('app_blog_show', ['id' => $postId]);
    }

    #[Route('/post/add', name: 'app_blog_add')]
    /**
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getUser());
            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('blog/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}

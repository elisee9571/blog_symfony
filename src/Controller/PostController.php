<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="post")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $post = new Post();
        $post->setCreatedAt("2020-02-01");
        $post->setUser($this->getUser());

        $formPost = $this->createForm(PostType::class, $post);
        $formPost->handleRequest($request);

        if ($formPost->isSubmitted() && $formPost->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($formPost->getData());
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('post/index.html.twig', [
            'formPost' => $formPost->createView(),
        ]);
    }

    /**
     * @Route("/post/update/{id}", name="post_update")
     */
    public function updatePost(Request $request, ManagerRegistry $doctrine, PostRepository $postRepository): Response
    {
        $em = $doctrine->getManager();
        $post = $postRepository->findOneBy(['id' => $request->get('id')]);

        if ($post) {
            $formUpdatePost = $this->createForm(PostType::class, $post);
            $formUpdatePost->handleRequest($request);

            if ($formUpdatePost->isSubmitted() && $formUpdatePost->isValid()) {
                $em = $doctrine->getManager();
                $em->persist($formUpdatePost->getData());
                $em->flush();

                return $this->redirectToRoute('home');
            }
        } else {
            return $this->redirectToRoute('home');
        }

        return $this->render('post/edit.html.twig', [
            'formUpdatePost' => $formUpdatePost->createView(),
            'post' => $post
        ]);
    }

    /**
     * @Route("/post/delete/{id}", name="post_delete")
     */
    public function deletePost(Request $request, PostRepository $postRepository, ManagerRegistry $doctrine): Response
    {
        $post = $postRepository->findOneBy(['id' => $request->get('id')]);

        if ($post) {
            $em = $doctrine->getManager();
            $postRepository->remove($post);
            $em->flush();

            return $this->redirectToRoute('home');
        }
    }
}

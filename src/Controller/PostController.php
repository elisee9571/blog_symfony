<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
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
}

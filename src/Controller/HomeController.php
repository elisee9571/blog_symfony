<?php

namespace App\Controller;

use App\Form\PostType;
use App\Form\CategoryType;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PostRepository $postRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'posts' => $postRepository->findAll()
        ]);
    }

    /**
     * @Route("/category/delete/{id}", name="category_delete")
     */
    public function delete(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['id' => $request->get('id')]);

        if ($category) {
            $categoryRepository->remove($category);

            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/category/update/{id}", name="category_update")
     */
    public function update(Request $request, ManagerRegistry $doctrine, CategoryRepository $categoryRepository): Response
    {
        $em = $doctrine->getManager();
        $category = $categoryRepository->findOneBy(['id' => $request->get('id')]);

        if ($category) {
            $formUpdate = $this->createForm(CategoryType::class, $category);
            $formUpdate->handleRequest($request);

            if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
                $em = $doctrine->getManager();
                $em->persist($formUpdate->getData());
                $em->flush();

                return $this->redirectToRoute('home');
            }
        } else {
            return $this->redirectToRoute('home');
        }

        return $this->render('category/edit.html.twig', [
            'formUpdate' => $formUpdate->createView()
        ]);
    }

    /**
     * @Route("/post/delete/{id}", name="post_delete")
     */
    public function deletePost(Request $request, PostRepository $postRepository): Response
    {
        $post = $postRepository->findOneBy(['id' => $request->get('id')]);

        if ($post) {
            $postRepository->remove($post);

            return $this->redirectToRoute('home');
        }
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
            'formUpdatePost' => $formUpdatePost->createView()
        ]);
    }
}

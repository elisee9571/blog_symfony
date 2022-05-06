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

        if ($this->getUser()) {
            return $this->render('home/index.html.twig', [
                'posts' => $postRepository->findById($this->getUser()),
                'postsAll' => $postRepository->findByBool()
            ]);
        }

        return $this->render('home/index.html.twig', [
            'posts' => $postRepository->findByBool()
        ]);
    }
}

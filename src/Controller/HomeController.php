<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
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
    public function index(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('home/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/delete/{id}", name="category_delete")
     */
    public function delete(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $category = $em->getRepository(Category::class)->findOneBy(['id' => $request->get('id')]);

        if ($category) {
            $em->getRepository(Category::class)->remove($category);

            return $this->redirectToRoute('home');
        }

        return $this->render('category/index.html.twig', []);
    }

    /**
     * @Route("/category/update/{id}", name="category_update")
     */
    public function update(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $category = $em->getRepository(Category::class)->findOneBy(['id' => $request->get('id')]);

        if ($category) {
            $form_update = $this->createForm(CategoryType::class, $category);
            $form_update->handleRequest($request);

            if ($form_update->isSubmitted() && $form_update->isValid()) {
                $em = $doctrine->getManager();
                $em->persist($form_update->getData());
                $em->flush();

                return $this->redirectToRoute('home');
            }
        } else {
            return $this->redirectToRoute('home');
        }

        return $this->render('category/edit.html.twig', [
            'form_update' => $form_update->createView()
        ]);
    }
}

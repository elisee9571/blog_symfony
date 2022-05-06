<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $category = new Category;

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('category/index.html.twig', [
            'form' => $form->createView()
        ]);
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
}

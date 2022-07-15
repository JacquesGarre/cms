<?php

namespace App\Controller;

use App\Entity\FrontOfficePage;
use App\Form\FrontOfficePageType;
use App\Repository\FrontOfficePageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/pages', priority: 1)]
class FrontOfficePageController extends AbstractController
{
    #[Route('/', name: 'app_front_office_page_index', methods: ['GET'])]
    public function index(FrontOfficePageRepository $frontOfficePageRepository): Response
    {
        return $this->render('front_office_page/index.html.twig', [
            'front_office_pages' => $frontOfficePageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_front_office_page_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FrontOfficePageRepository $frontOfficePageRepository): Response
    {
        $frontOfficePage = new FrontOfficePage();
        $form = $this->createForm(FrontOfficePageType::class, $frontOfficePage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $frontOfficePageRepository->add($frontOfficePage, true);

            return $this->redirectToRoute('app_front_office_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front_office_page/new.html.twig', [
            'front_office_page' => $frontOfficePage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_front_office_page_show', methods: ['GET'])]
    public function show(FrontOfficePage $frontOfficePage): Response
    {
        return $this->render('front_office_page/show.html.twig', [
            'front_office_page' => $frontOfficePage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_front_office_page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FrontOfficePage $frontOfficePage, FrontOfficePageRepository $frontOfficePageRepository): Response
    {
        $form = $this->createForm(FrontOfficePageType::class, $frontOfficePage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $frontOfficePageRepository->add($frontOfficePage, true);

            return $this->redirectToRoute('app_front_office_page_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front_office_page/edit.html.twig', [
            'front_office_page' => $frontOfficePage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_front_office_page_delete', methods: ['POST'])]
    public function delete(Request $request, FrontOfficePage $frontOfficePage, FrontOfficePageRepository $frontOfficePageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frontOfficePage->getId(), $request->request->get('_token'))) {
            $frontOfficePageRepository->remove($frontOfficePage, true);
        }

        return $this->redirectToRoute('app_front_office_page_index', [], Response::HTTP_SEE_OTHER);
    }
}

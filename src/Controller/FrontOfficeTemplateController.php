<?php

namespace App\Controller;

use App\Entity\FrontOfficeTemplate;
use App\Form\FrontOfficeTemplateType;
use App\Repository\FrontOfficeTemplateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/templates')]
class FrontOfficeTemplateController extends AbstractController
{

    public function __construct(\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }

    #[Route('/', name: 'app_front_office_template_index', methods: ['GET'])]
    public function index(FrontOfficeTemplateRepository $frontOfficeTemplateRepository): Response
    {
        return $this->render('front_office_template/index.html.twig', [
            'front_office_templates' => $frontOfficeTemplateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_front_office_template_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FrontOfficeTemplateRepository $frontOfficeTemplateRepository): Response
    {
        $frontOfficeTemplate = new FrontOfficeTemplate();
        $form = $this->createForm(FrontOfficeTemplateType::class, $frontOfficeTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $frontOfficeTemplateRepository->add($frontOfficeTemplate, true);

            return $this->redirectToRoute('app_front_office_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front_office_template/new.html.twig', [
            'front_office_template' => $frontOfficeTemplate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_front_office_template_show', methods: ['GET'])]
    public function show(FrontOfficeTemplate $frontOfficeTemplate): Response
    {
        $htmlTemplate = $this->twig->createTemplate($frontOfficeTemplate->getHtml());
        return $this->render('front_office_template/show.html.twig', [
            'front_office_template' => $frontOfficeTemplate,
            'html_template' => $htmlTemplate->render()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_front_office_template_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FrontOfficeTemplate $frontOfficeTemplate, FrontOfficeTemplateRepository $frontOfficeTemplateRepository): Response
    {
        $form = $this->createForm(FrontOfficeTemplateType::class, $frontOfficeTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $frontOfficeTemplateRepository->add($frontOfficeTemplate, true);

            return $this->redirectToRoute('app_front_office_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front_office_template/edit.html.twig', [
            'front_office_template' => $frontOfficeTemplate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_front_office_template_delete', methods: ['POST'])]
    public function delete(Request $request, FrontOfficeTemplate $frontOfficeTemplate, FrontOfficeTemplateRepository $frontOfficeTemplateRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$frontOfficeTemplate->getId(), $request->request->get('_token'))) {
            $frontOfficeTemplateRepository->remove($frontOfficeTemplate, true);
        }

        return $this->redirectToRoute('app_front_office_template_index', [], Response::HTTP_SEE_OTHER);
    }
}

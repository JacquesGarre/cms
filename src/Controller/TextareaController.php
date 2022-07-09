<?php

namespace App\Controller;

use App\Entity\Textarea;
use App\Form\TextareaType;
use App\Repository\TextareaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/textarea')]
class TextareaController extends AbstractController
{
    #[Route('/', name: 'app_textarea_index', methods: ['GET'])]
    public function index(TextareaRepository $textareaRepository): Response
    {
        return $this->render('textarea/index.html.twig', [
            'textareas' => $textareaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_textarea_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TextareaRepository $textareaRepository): Response
    {
        $textarea = new Textarea();
        $form = $this->createForm(TextareaType::class, $textarea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $textareaRepository->add($textarea, true);
            return $this->redirectToRoute('app_form_show', ['id' => $textarea->getForm()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('textarea/new.html.twig', [
            'textarea' => $textarea,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_textarea_show', methods: ['GET'])]
    public function show(Textarea $textarea): Response
    {
        return $this->render('textarea/show.html.twig', [
            'textarea' => $textarea,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_textarea_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Textarea $textarea, TextareaRepository $textareaRepository): Response
    {
        $form = $this->createForm(TextareaType::class, $textarea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $textareaRepository->add($textarea, true);

            return $this->redirectToRoute('app_textarea_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('textarea/edit.html.twig', [
            'textarea' => $textarea,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_textarea_delete', methods: ['POST'])]
    public function delete(Request $request, Textarea $textarea, TextareaRepository $textareaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$textarea->getId(), $request->request->get('_token'))) {
            $textareaRepository->remove($textarea, true);
        }

        return $this->redirectToRoute('app_textarea_index', [], Response::HTTP_SEE_OTHER);
    }
}

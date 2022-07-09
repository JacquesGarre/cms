<?php

namespace App\Controller;

use App\Entity\Form;
use App\Form\FormType;
use App\Repository\FormRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/models')]
class FormController extends AbstractController
{
    #[Route('/', name: 'app_form_index', methods: ['GET'])]
    public function index(FormRepository $formRepository): Response
    {
        return $this->render('form/index.html.twig', [
            'forms' => $formRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_form_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FormRepository $formRepository): Response
    {
        $formEntity = new Form();
        $form = $this->createForm(FormType::class, $formEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formRepository->add($formEntity, true);

            return $this->redirectToRoute('app_form_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('form/new.html.twig', [
            'formEntity' => $formEntity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_form_show', methods: ['GET'])]
    public function show(Form $form): Response
    {
        return $this->render('form/show.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_form_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Form $formEntity, FormRepository $formRepository): Response
    {
        $form = $this->createForm(FormType::class, $formEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formRepository->add($formEntity, true);

            return $this->redirectToRoute('app_form_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('form/edit.html.twig', [
            'formEntity' => $formEntity,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_form_delete', methods: ['POST'])]
    public function delete(Request $request, Form $form, FormRepository $formRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$form->getId(), $request->request->get('_token'))) {
            $formRepository->remove($form, true);
        }

        return $this->redirectToRoute('app_form_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\Input;
use App\Form\InputType;
use App\Repository\InputRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/input')]
class InputController extends AbstractController
{
    #[Route('/', name: 'app_input_index', methods: ['GET'])]
    public function index(InputRepository $inputRepository): Response
    {
        return $this->render('input/index.html.twig', [
            'inputs' => $inputRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_input_new', methods: ['GET', 'POST'])]
    public function new(Request $request, InputRepository $inputRepository): Response
    {
        $input = new Input();
        $form = $this->createForm(InputType::class, $input);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputRepository->add($input, true);

            return $this->redirectToRoute('app_form_show', ['id' => $input->getForm()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('input/new.html.twig', [
            'input' => $input,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_input_show', methods: ['GET'])]
    public function show(Input $input): Response
    {
        return $this->render('input/show.html.twig', [
            'input' => $input,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_input_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Input $input, InputRepository $inputRepository): Response
    {
        $form = $this->createForm(InputType::class, $input);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $inputRepository->add($input, true);

            return $this->redirectToRoute('app_input_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('input/edit.html.twig', [
            'input' => $input,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_input_delete', methods: ['POST'])]
    public function delete(Request $request, Input $input, InputRepository $inputRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$input->getId(), $request->request->get('_token'))) {
            $inputRepository->remove($input, true);
        }

        return $this->redirectToRoute('app_input_index', [], Response::HTTP_SEE_OTHER);
    }
}

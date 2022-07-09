<?php

namespace App\Controller;

use App\Entity\Select;
use App\Form\SelectType;
use App\Repository\SelectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/select')]
class SelectController extends AbstractController
{
    #[Route('/', name: 'app_select_index', methods: ['GET'])]
    public function index(SelectRepository $selectRepository): Response
    {
        return $this->render('select/index.html.twig', [
            'selects' => $selectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_select_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SelectRepository $selectRepository): Response
    {
        $select = new Select();
        $form = $this->createForm(SelectType::class, $select);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectRepository->add($select, true);

            return $this->redirectToRoute('app_select_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('select/new.html.twig', [
            'select' => $select,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_select_show', methods: ['GET'])]
    public function show(Select $select): Response
    {
        return $this->render('select/show.html.twig', [
            'select' => $select,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_select_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Select $select, SelectRepository $selectRepository): Response
    {
        $form = $this->createForm(SelectType::class, $select);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectRepository->add($select, true);

            return $this->redirectToRoute('app_select_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('select/edit.html.twig', [
            'select' => $select,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_select_delete', methods: ['POST'])]
    public function delete(Request $request, Select $select, SelectRepository $selectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$select->getId(), $request->request->get('_token'))) {
            $selectRepository->remove($select, true);
        }

        return $this->redirectToRoute('app_select_index', [], Response::HTTP_SEE_OTHER);
    }
}

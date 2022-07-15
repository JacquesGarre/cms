<?php

namespace App\Controller;

use App\Entity\Index;
use App\Form\IndexType;
use App\Repository\FormRepository;
use App\Repository\IndexRepository;
use App\Repository\IndexColumnRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/models')]
class IndexController extends AbstractController
{
    #[Route('/{id}/indices', name: 'app_index_index', methods: ['GET'])]
    public function index(IndexRepository $indexRepository, FormRepository $formRepository, int $id): Response
    {   
        $model = $formRepository->find($id);
        return $this->render('index/index.html.twig', [
            'indices' => $indexRepository->findAll(),
            'model' => $model
        ]);
    }

    #[Route('/{id}/indices/new', name: 'app_index_new', methods: ['GET', 'POST'])]
    public function new(Request $request, IndexRepository $indexRepository, FormRepository $formRepository, int $id): Response
    {   

        $model = $formRepository->find($id);

        $index = new Index();
        $index->setModel($model);
        
        $form = $this->createForm(IndexType::class, $index);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $indexRepository->add($index, true);
            $session = $request->getSession();
            $session->clear();
            return $this->redirectToRoute('app_index_edit', [
                'form_id' => $model->getId(),
                'id' => $index->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('index/new.html.twig', [
            'index' => $index,
            'form' => $form,
            'model' => $model
        ]);
    }

    #[Route('/{id}/indices/', name: 'app_index_show', methods: ['GET'])]
    public function show(Index $index, FormRepository $formRepository, int $id): Response
    {
        return $this->render('index/show.html.twig', [
            'index' => $index,
        ]);
    }

    #[Route('/{form_id}/indices/{id}/edit', name: 'app_index_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Index $index, IndexRepository $indexRepository, FormRepository $formRepository, int $form_id, IndexColumnRepository $columnRepository): Response
    {
        $indexColumns = $columnRepository->findBy(['view' => $index]);
        $model = $formRepository->find($form_id);
        $form = $this->createForm(IndexType::class, $index);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $indexRepository->add($index, true);
            $session = $request->getSession();
            $session->clear();

            return $this->redirectToRoute('app_form_edit', ['id' => $model->getId()],  Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('index/edit.html.twig', [
            'index' => $index,
            'form' => $form,
            'model' => $model,
            'index_columns' => $indexColumns
        ]);
    }

    #[Route('/{form_id}/indices/{id}', name: 'app_index_delete', methods: ['POST'])]
    public function delete(Request $request, Index $index, IndexRepository $indexRepository, FormRepository $formRepository, int $form_id): Response
    {
        $model = $formRepository->find($form_id);
        if ($this->isCsrfTokenValid('delete'.$index->getId(), $request->request->get('_token'))) {
            $indexRepository->remove($index, true);
            $session = $request->getSession();
            $session->clear();
        }

        return $this->redirectToRoute('app_form_edit', ['id' => $model->getId()],  Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\IndexColumn;
use App\Form\IndexColumnType;
use App\Repository\IndexColumnRepository;
use App\Repository\FormRepository;
use App\Repository\IndexRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/models/{model_id}/indices/{index_id}/column')]
class IndexColumnController extends AbstractController
{
    #[Route('/', name: 'app_index_column_index', methods: ['GET'])]
    public function index(IndexColumnRepository $indexColumnRepository, FormRepository $formRepository, IndexRepository $indexRepository, int $model_id, int $index_id): Response
    {
        $model = $formRepository->find($model_id);
        $index = $indexRepository->find($index_id);
        return $this->render('index_column/index.html.twig', [
            'index_columns' => $indexColumnRepository->findBy(['view' => $index]),
            'model' => $model,
            'index' => $index
        ]);
    }

    #[Route('/new', name: 'app_index_column_new', methods: ['GET', 'POST'])]
    public function new(Request $request, IndexColumnRepository $indexColumnRepository, FormRepository $formRepository, IndexRepository $indexRepository, int $model_id, int $index_id): Response
    {
        $model = $formRepository->find($model_id);
        $index = $indexRepository->find($index_id);

        $indexColumn = new IndexColumn();
        $indexColumn->setView($index);
        $form = $this->createForm(IndexColumnType::class, $indexColumn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $indexColumnRepository->add($indexColumn, true);

            return $this->redirectToRoute('app_index_edit', [
                'form_id' => $model->getId(),
                'id' => $index->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('index_column/new.html.twig', [
            'index_column' => $indexColumn,
            'form' => $form,
            'model' => $model,
            'index' => $index
        ]);
    }

    #[Route('/{id}', name: 'app_index_column_show', methods: ['GET'])]
    public function show(IndexColumn $indexColumn): Response
    {
        return $this->render('index_column/show.html.twig', [
            'index_column' => $indexColumn,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_index_column_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IndexColumn $indexColumn, IndexColumnRepository $indexColumnRepository, FormRepository $formRepository, IndexRepository $indexRepository, int $model_id, int $index_id): Response
    {   
        $model = $formRepository->find($model_id);
        $index = $indexRepository->find($index_id);
        $form = $this->createForm(IndexColumnType::class, $indexColumn);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $indexColumnRepository->add($indexColumn, true);

            return $this->redirectToRoute('app_index_edit', [
                'form_id' => $model->getId(),
                'id' => $index->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('index_column/edit.html.twig', [
            'index_column' => $indexColumn,
            'form' => $form,
            'model' => $model,
            'index' => $index
        ]);
    }

    #[Route('/{id}', name: 'app_index_column_delete', methods: ['POST'])]
    public function delete(Request $request, IndexColumn $indexColumn, IndexColumnRepository $indexColumnRepository, FormRepository $formRepository, IndexRepository $indexRepository, int $model_id, int $index_id): Response
    {
        $model = $formRepository->find($model_id);
        $index = $indexRepository->find($index_id);
        if ($this->isCsrfTokenValid('delete'.$indexColumn->getId(), $request->request->get('_token'))) {
            $indexColumnRepository->remove($indexColumn, true);
        }
        return $this->redirectToRoute('app_form_edit', ['id' => $model->getId()], Response::HTTP_SEE_OTHER);
    }
}

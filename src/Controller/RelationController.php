<?php

namespace App\Controller;

use App\Entity\Relation;
use App\Form\RelationType;
use App\Repository\RelationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FormRepository;

#[Route('/admin/relation/{model_id}')]
class RelationController extends AbstractController
{
    #[Route('/', name: 'app_relation_index', methods: ['GET'])]
    public function index(RelationRepository $relationRepository): Response
    {
        return $this->render('relation/index.html.twig', [
            'relations' => $relationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_relation_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        RelationRepository $relationRepository,
        FormRepository $formRepository, 
        int $model_id): Response
    {

        $relation = new Relation();
        $form = $this->createForm(RelationType::class, $relation);
        $form->handleRequest($request);
        $model = $formRepository->find($model_id);

        if ($form->isSubmitted() && $form->isValid()) {

            $relation->setModel($model);
            $relationRepository->add($relation, true);
            $session = $request->getSession();
            $session->clear();

            return $this->redirectToRoute('app_form_edit', ['id' => $model->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('relation/new.html.twig', [
            'relation' => $relation,
            'form' => $form,
            'model' => $model
        ]);
    }

    #[Route('/{id}', name: 'app_relation_show', methods: ['GET'])]
    public function show(Relation $relation): Response
    {
        return $this->render('relation/show.html.twig', [
            'relation' => $relation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_relation_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Relation $relation, 
        RelationRepository $relationRepository,
        FormRepository $formRepository, 
        int $model_id
    ): Response        
    {   
        $model = $formRepository->find($model_id);
        $form = $this->createForm(RelationType::class, $relation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $relationRepository->add($relation, true);
            $session = $request->getSession();
            $session->clear();
            return $this->redirectToRoute('app_form_edit', ['id' => $model->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('relation/edit.html.twig', [
            'relation' => $relation,
            'form' => $form,
            'model' => $model
        ]);
    }

    #[Route('/{id}', name: 'app_relation_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Relation $relation, 
        RelationRepository $relationRepository,
        FormRepository $formRepository, 
        int $model_id
    ): Response
    {   
        $model = $formRepository->find($model_id);
        if ($this->isCsrfTokenValid('delete'.$relation->getId(), $request->request->get('_token'))) {
            $session = $request->getSession();
            $session->clear();
            $relationRepository->remove($relation, true);
        }
        return $this->redirectToRoute('app_form_edit', ['id' => $model->getId()], Response::HTTP_SEE_OTHER);

    }
}

<?php

namespace App\Controller;

use App\Entity\Form;
use App\Form\FormType;
use App\Repository\FormRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AttributeRepository;
use App\Repository\IndexRepository;
use App\Repository\MenuItemRepository;
use App\Repository\IndexColumnRepository;
use App\Repository\RelationRepository;

#[Route('/admin/models', priority: 1)]
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

            return $this->redirectToRoute('app_form_edit', ['id' => $formEntity->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('form/new.html.twig', [
            'formEntity' => $formEntity,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'app_form_show', methods: ['GET'])]
    // public function show(Form $form, AttributeRepository $attributeRepository): Response
    // {   
    //     $attributes = $attributeRepository->findBy(['form' => $form]);
    //     return $this->render('form/show.html.twig', [
    //         'form' => $form,
    //         'model' => $form,
    //         'attributes' => $attributes 
    //     ]);
    // }

    #[Route('/{id}', name: 'app_form_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Form $model, 
        FormRepository $formRepository, 
        AttributeRepository $attributeRepository, 
        IndexRepository $indexRepository,
        RelationRepository $relationRepository
    ): Response
    {       
        $indices = $indexRepository->findBy(['model' => $model]);
        $attributes = $attributeRepository->findBy(['form' => $model], ['position' => 'ASC']);
        $form = $this->createForm(FormType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formRepository->add($model, true);
            $session = $request->getSession();
            $session->clear();

            return $this->redirectToRoute('app_form_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('form/edit.html.twig', [
            'model' => $model,
            'form' => $form,
            'attributes' => $attributes,
            'indices' => $indices,
            'relations' => $relationRepository->findBy(['model' => $model], ['position' => 'ASC']),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_form_delete', methods: ['POST'])]
    public function delete(Request $request, Form $form, FormRepository $formRepository, MenuItemRepository $menuRepository, IndexRepository $indexRepository, AttributeRepository $attributeRepository, IndexColumnRepository $colRepository): Response
    {

        if ($this->isCsrfTokenValid('delete'.$form->getId(), $request->request->get('_token'))) {
            $session = $request->getSession();
            $session->clear();
            $formRepository->remove($form, true);
        }

        return $this->redirectToRoute('app_form_index', [], Response::HTTP_SEE_OTHER);
    }
}

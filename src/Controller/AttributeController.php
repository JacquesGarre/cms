<?php

namespace App\Controller;

use App\Entity\Attribute;
use App\Form\AttributeType;
use App\Repository\AttributeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FormRepository;

#[Route('/models')]
class AttributeController extends AbstractController
{
    #[Route('/{id}/attributes', name: 'app_attribute_index', methods: ['GET'])]
    public function index(AttributeRepository $attributeRepository, FormRepository $formRepository, int $id): Response
    {   
        $model = $formRepository->find($id);
        return $this->render('attribute/index.html.twig', [
            'attributes' => $attributeRepository->findBy(['form' => $model], ['position' => 'ASC']),
            'model' => $model
        ]);
    }

    #[Route('/{id}/attributes/new', name: 'app_attribute_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AttributeRepository $attributeRepository, FormRepository $formRepository, int $id): Response
    {
        $model = $formRepository->find($id);

        $attribute = new Attribute();
        $form = $this->createForm(AttributeType::class, $attribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $attribute->setForm($model);
            $attributeRepository->add($attribute, true);
            return $this->redirectToRoute('app_form_edit', ['id' => $model->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('attribute/new.html.twig', [
            'attribute' => $attribute,
            'form' => $form,
            'model' => $model
        ]);
    }

    #[Route('/{form_id}/attributes/{id}', name: 'app_attribute_show', methods: ['GET'])]
    public function show(Attribute $attribute, FormRepository $formRepository, int $form_id): Response
    {   
        $model = $formRepository->find($form_id);
        return $this->render('attribute/show.html.twig', [
            'attribute' => $attribute,
            'model' => $model
        ]);
    }

    #[Route('/{form_id}/attributes/{id}/edit', name: 'app_attribute_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Attribute $attribute, AttributeRepository $attributeRepository, FormRepository $formRepository, int $form_id): Response
    {   
        $model = $formRepository->find($form_id);
        $form = $this->createForm(AttributeType::class, $attribute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $attributeRepository->add($attribute, true);

            return $this->redirectToRoute('app_form_edit', ['id' => $model->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('attribute/edit.html.twig', [
            'attribute' => $attribute,
            'form' => $form,
            'model' => $model,
            'options' => $attribute->getOptions()
        ]);
    }

    #[Route('/{form_id}/attributes/{id}/delete', name: 'app_attribute_delete', methods: ['POST'])]
    public function delete(Request $request, Attribute $attribute, AttributeRepository $attributeRepository, FormRepository $formRepository, int $form_id): Response
    {   
        $session = $request->getSession();
        $model = $formRepository->find($form_id);
        if ($this->isCsrfTokenValid('delete'.$attribute->getId(), $request->request->get('_token'))) {
            $attributeRepository->remove($attribute, true);
            $session->clear();
        }

        return $this->redirectToRoute('app_form_edit', ['id' => $model->getId()], Response::HTTP_SEE_OTHER);
    }
}

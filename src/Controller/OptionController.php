<?php

namespace App\Controller;

use App\Entity\Option;
use App\Form\OptionType;
use App\Repository\OptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AttributeRepository;

#[Route('{attribute_id}/option')]
class OptionController extends AbstractController
{
    #[Route('/', name: 'app_option_index', methods: ['GET'])]
    public function index(OptionRepository $optionRepository, AttributeRepository $attributeRepository, int $attribute_id): Response
    {
        $attribute = $attributeRepository->find($attribute_id);
        return $this->render('option/index.html.twig', [
            'options' => $attribute->getOptions(),
            'attribute' => $attribute
        ]);
    }

    #[Route('/new', name: 'app_option_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OptionRepository $optionRepository, AttributeRepository $attributeRepository, int $attribute_id): Response
    {
        $attribute = $attributeRepository->find($attribute_id);
        $option = new Option();
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $optionRepository->add($option, true);
            $attribute->addOption($option);
            $attributeRepository->add($attribute, true);
            return $this->redirectToRoute('app_attribute_edit', [
                'form_id' => $attribute->getForm()->getId(),
                'id' => $attribute->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('option/new.html.twig', [
            'option' => $option,
            'form' => $form,
            'attribute' => $attribute
        ]);
    }

    #[Route('/{id}', name: 'app_option_show', methods: ['GET'])]
    public function show(Option $option): Response
    {
        return $this->render('option/show.html.twig', [
            'option' => $option,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_option_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Option $option, OptionRepository $optionRepository, AttributeRepository $attributeRepository, int $attribute_id): Response
    {
        $attribute = $attributeRepository->find($attribute_id);
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $optionRepository->add($option, true);

            return $this->redirectToRoute('app_attribute_edit', [
                'form_id' => $attribute->getForm()->getId(),
                'id' => $attribute->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('option/edit.html.twig', [
            'option' => $option,
            'form' => $form,
            'attribute' => $attribute
        ]);
    }

    #[Route('/{id}', name: 'app_option_delete', methods: ['POST'])]
    public function delete(Request $request, Option $option, OptionRepository $optionRepository, AttributeRepository $attributeRepository, int $attribute_id): Response
    {   
        $attribute = $attributeRepository->find($attribute_id);
        if ($this->isCsrfTokenValid('delete'.$option->getId(), $request->request->get('_token'))) {
            $optionRepository->remove($option, true);
        }

        return $this->redirectToRoute('app_attribute_edit', [
            'form_id' => $attribute->getForm()->getId(),
            'id' => $attribute->getId()
        ], Response::HTTP_SEE_OTHER);
    }
}

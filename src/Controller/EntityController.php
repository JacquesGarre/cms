<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Entity\EntityMeta;
use App\Form\EntityType;
use App\Repository\EntityRepository;
use App\Repository\EntityMetaRepository;
use App\Repository\FormRepository;
use App\Repository\IndexRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/entity')]
class EntityController extends AbstractController
{
    #[Route('/{id}/{view_id}', name: 'app_entity_index', methods: ['GET'])]
    public function index(EntityRepository $entityRepository, FormRepository $formRepository, int $id, IndexRepository $indexRepository, int $view_id): Response
    {
        $model = $formRepository->find($id);
        $view = $indexRepository->find($view_id);

        $entities = $entityRepository->findBy(['model' => $model]);

        return $this->render('entity/index.html.twig', [
            'entities' => $entities,
            'model' => $model,
            'view' => $view
        ]);
    }

    #[Route('/{id}/{view_id}/new', name: 'app_entity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityRepository $entityRepository, EntityMetaRepository $entityMetaRepository, FormRepository $formRepository, int $id, IndexRepository $indexRepository, int $view_id): Response
    {
    
        $model = $formRepository->find($id);
        $view = $indexRepository->find($view_id);

        $entity = new Entity();
        $entity->setModel($model);

        $form = $this->createForm(EntityType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $creationDate = new \DateTime();
            $entity->setCreationDate($creationDate);
            $entityRepository->add($entity, true);

            $fields = $model->getAttributes();
            foreach($fields as $field){
                $fieldID = $field->getName();
                $fieldValue = $form->get($fieldID)->getData();
                $meta = new EntityMeta();
                $meta->setEntity($entity);
                $meta->setName($fieldID);
                $meta->setValue($fieldValue);
                $entityMetaRepository->add($meta, true);
            }

            return $this->redirectToRoute('app_entity_index', [
                'id' => $model->getId(),
                'view_id' => $view->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('entity/new.html.twig', [
            'entity' => $entity,
            'form' => $form,
            'model' => $model,
            'view' => $view
        ]);
    }

    // #[Route('/{form_id}/{view_id}/details/{id}', name: 'app_entity_show', methods: ['GET'])]
    // public function show(Entity $entity, FormRepository $formRepository, int $form_id): Response
    // {
    //     return $this->render('entity/show.html.twig', [
    //         'entity' => $entity,
    //     ]);
    // }

    #[Route('/{form_id}/{view_id}/details/{id}', name: 'app_entity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Entity $entity, EntityRepository $entityRepository, EntityMetaRepository $entityMetaRepository, FormRepository $formRepository, int $form_id, IndexRepository $indexRepository, int $view_id): Response
    {
        $model = $formRepository->find($form_id);
        $fields = $model->getAttributes();
        $view = $indexRepository->find($view_id);

        $form = $this->createForm(EntityType::class, $entity);
        foreach($entity->getEntityMetas() as $meta){
            $form->get($meta->getName())->setData($meta->getValue());
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $updateDate = new \DateTime();
            $entity->setUpdateDate($updateDate);
            $entityRepository->add($entity, true);
            
            foreach($fields as $field){
                $fieldID = $field->getName();
                $fieldValue = $form->get($fieldID)->getData();
                $meta = $entity->getEntityMeta($fieldID);
                if(empty($meta)){
                    $meta = new EntityMeta();
                    $meta->setEntity($entity);
                    $meta->setName($fieldID);
                }
                $meta->setValue($fieldValue);
                $entityMetaRepository->add($meta, true);
            }

            return $this->redirectToRoute('app_entity_index', [
                'id' => $model->getId(),
                'view_id' => $view->getId(),
                'model' => $model
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('entity/edit.html.twig', [
            'entity' => $entity,
            'form' => $form,
            'model' => $model,
            'view' => $view
        ]);
    }

    #[Route('/{form_id}/{view_id}/details/{id}/delete', name: 'app_entity_delete', methods: ['POST'])]
    public function delete(Request $request, Entity $entity, EntityRepository $entityRepository, FormRepository $formRepository, int $form_id, IndexRepository $indexRepository, int $view_id): Response
    {

        $model = $formRepository->find($form_id);
        $view = $indexRepository->find($view_id);

        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $entityRepository->remove($entity, true);
        }

        return $this->redirectToRoute('app_entity_index', [
            'id' => $model->getId(),
            'view_id' => $view->getId()
        ], Response::HTTP_SEE_OTHER);
    }
}

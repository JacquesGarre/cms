<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Entity\EntityMeta;
use App\Form\EntityFormType;
use App\Repository\AttributeRepository;
use App\Repository\EntityRepository;
use App\Repository\EntityMetaRepository;
use App\Repository\FormRepository;
use App\Repository\IndexRepository;
use App\Repository\OptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/entity')]
class EntityController extends AbstractController
{
    #[Route('/{id}/{view_id}', name: 'app_entity_index', methods: ['GET'])]
    public function index(Request $request, EntityRepository $entityRepository, FormRepository $formRepository, int $id, IndexRepository $indexRepository, int $view_id): Response
    {
        $currentPage = !empty($request->query->get('page')) ? $request->query->get('page') : 1;
        $model = $formRepository->find($id);
        $view = $indexRepository->find($view_id);

        $limit = !empty($view->getPagination()) ? $view->getPagination() : null;
        $offset = $limit ? ($currentPage-1) * $limit : 0 ;
        $allEntities = $entityRepository->findBy(['model' => $model]);
        $pages = $limit ? ceil(count($allEntities)/$limit) : false;

        // Query data for this view
        $entities = $entityRepository->findByView($view, $currentPage);

        $patterns = [];
        $externalEntities = [];
        foreach($view->getIndexColumns() as $column){   
            if(
                $column->getField()->getType() == 'select' 
                && (
                    $column->getField()->getSelectEntity() !== 'options' or !empty($column->getField()->getSelectEntity())
                ) 
            ){  
                $entityID = $column->getField()->getSelectEntity();
                $entity = $formRepository->find($entityID);
                if(!empty($entity)){
                    $patterns[$entity->getId()] = $entity->getDisplayPattern();
                    $externalEntities[$entity->getId()] = $entityRepository->findBy(['model' => $entity]);
                }
            }
            $patterns[$column->getField()->getForm()->getId()] = $column->getField()->getForm()->getDisplayPattern();
            $externalEntities[$column->getField()->getForm()->getId()] = $entityRepository->findBy(['model' => $column->getField()->getForm()]);
        }

        return $this->render('entity/index.html.twig', [
            'entities' => $entities,
            'model' => $model,
            'view' => $view,
            'patterns' => $patterns,
            'externalEntities' => $externalEntities,
            'pages' => $pages,
            'total' => count($allEntities)
        ]);
    }

    #[Route('/{id}/{view_id}/new', name: 'app_entity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityRepository $entityRepository, EntityMetaRepository $entityMetaRepository, FormRepository $formRepository, int $id, IndexRepository $indexRepository, int $view_id): Response
    {
    
        $model = $formRepository->find($id);
        $view = $indexRepository->find($view_id);

        $entity = new Entity();
        $entity->setModel($model);

        $form = $this->createForm(EntityFormType::class, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $creationDate = new \DateTime();
            $entity->setCreationDate($creationDate);
            $entityRepository->add($entity, true);

            $fields = $model->getAttributes();
            foreach($fields as $field){
                $fieldID = $field->getName();
                $fieldValue = $form->get($fieldID)->getViewData();
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
    public function edit(
        Request $request, 
        Entity $entity, 
        EntityRepository $entityRepository, 
        EntityMetaRepository $entityMetaRepository, 
        FormRepository $formRepository, 
        int $form_id, 
        IndexRepository $indexRepository, 
        int $view_id,
        AttributeRepository $attributeRepository,
        OptionRepository $optionRepository): Response
    {
        $model = $formRepository->find($form_id);
        $view = $indexRepository->find($view_id);
        $form = $this->createForm(EntityFormType::class, $entity);
        foreach($entity->getEntityMetas() as $meta){

            $attribute = $attributeRepository->findOneBy([
                'name' => $meta->getName(),
                'form' => $model
            ]);
            if($attribute->getType() == 'select' && (empty($attribute->getSelectEntity()) || $attribute->getSelectEntity() == 'option')){
                $option = $optionRepository->findOneBy(['id' => $meta->getValue()]);
                $form->get($meta->getName())->setData($option);
            } else {
                $form->get($meta->getName())->setData($meta->getValue());
            }

        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $updateDate = new \DateTime();
            $entity->setUpdateDate($updateDate);
            $entityRepository->add($entity, true);

            $fields = $model->getAttributes();
            foreach($fields as $field){
                $fieldID = $field->getName();
                $fieldValue = $form->get($fieldID)->getViewData();
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

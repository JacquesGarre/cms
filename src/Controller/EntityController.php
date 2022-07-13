<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Entity\Option;
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
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;
use App\Repository\RelationRepository;

#[Route('/entity')]
class EntityController extends AbstractController
{
    #[Route('/{id}/{view_id}', name: 'app_entity_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request, 
        EntityRepository $entityRepository, 
        FormRepository $formRepository, 
        int $id, 
        IndexRepository $indexRepository, 
        int $view_id,
        FormFactoryInterface $formFactory,
        RelationRepository $relationRepository,
        $relation_id = false,
        $parent_id = false
    ): Response
    {
        $filters = [];
        if(!empty($request->request->all()['filters'])){
            $filters = array_filter($request->request->all()['filters']);
        }

        $relation = false;
        if(!empty($relation_id) && !empty($parent_id)){
            $relation = $relationRepository->find($relation_id);
            $filters[$relation->getMappedBy()->getName()] = $parent_id;
        }

        $page = !empty($request->query->get('page')) ? $request->query->get('page') : 1;
        $model = $formRepository->find($id);
        $view = $indexRepository->find($view_id);

        $limit = !empty($view->getPagination()) ? $view->getPagination() : null;
        $offset = $limit ? ($page-1) * $limit : 0;
        //$count = $entityRepository->findBy(['model' => $model]);

        $count = $entityRepository->getCountByView($view, $filters);
        $pages = $limit ? ceil($count/$limit) : false;

        // current order by
        $orderBy = false;
        if(!empty($request->query->get('order'))){
            $orderBy = $request->query->get('order');
        } elseif(!empty($view->getOrderBy())) {
            $orderBy = $view->getOrderBy()->getField()->getName();
        }

        // current order direction
        $orderDirection = false;
        if(!empty($request->query->get('direction'))){
            $orderDirection = $request->query->get('direction');
        } elseif(!empty($view->getOrderDirection())) {
            $orderDirection = $view->getOrderDirection();
        }

        // current order
        $order = [
            'order' => $orderBy,
            'direction' => $orderDirection ?? 'ASC'
        ];

        // Query data for this particular view
        $entities = $entityRepository->findByView($view, $page, $order, $filters);

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

        if(!$relation){
            $filterForm = $this->getFilterForm($entityRepository, $formRepository, $model, $formFactory);
            $filterForm->handleRequest($request);
            $resetForm = $this->getFilterForm($entityRepository, $formRepository, $model, $formFactory, true);
        } else {
            $filterForm = $this->getFilterForm($entityRepository, $formRepository, $relation->getView()->getModel(), $formFactory);
            $filterForm->handleRequest($request);
            $resetForm = $this->getFilterForm($entityRepository, $formRepository, $relation->getView()->getModel(), $formFactory, true);
        }


        $template = !empty($relation) ? 'entity/view.html.twig' : 'entity/index.html.twig';
        return $this->render($template, [
            'entities' => $entities,
            'model' => $model,
            'view' => $view,
            'patterns' => $patterns,
            'externalEntities' => $externalEntities,
            'pages' => $pages,
            'total' => $count,
            'order' => $order,
            'filterForm' => $filterForm->createView(),
            'resetForm' => $resetForm->createView(),
        ]);
    }

    private function getFilterForm(EntityRepository $entityRepository, FormRepository $formRepository, $model, FormFactoryInterface $formFactory, $reset = false)
    {
        $entity = new Entity();
        $entity->setModel($model);

        $filterForm = $formFactory->createNamed('filters', EntityFormType::class, $entity, [
            'csrf_protection' => false
        ]);

        $fields = $model->getAttributes();
        foreach($fields as $field){
            $options = [
                'mapped' => false,
                'label' => $field->getLabel(),
                'required' => false
            ];
            if(!$reset){
                switch($field->getType()){
                    case 'textarea':
                    case 'text':
                        $class = TextType::class;
                    break;
                    case 'select':
                        $class = EntityType::class;
    
                        if($field->getSelectEntity() == 'option' || empty($field->getSelectEntity())){
                            $options['class'] = Option::class;
                            $options['choices'] = $field->getOptions();
                            $options['choice_label'] = 'text';
                            $options['choice_value'] = function (?Option $entity) {
                                return $entity ? $entity->getId() : '';
                            };
                        } else {
    
                            $model = $formRepository->find($field->getSelectEntity());
                            $entities = $entityRepository->findBy(['model' => $model]);
                            $class = ChoiceType::class;
                            $options['choices'] = ['' => ''];
                            foreach($entities as $index => $entity)
                            {
                                $pattern = $model->getDisplayPattern();
                                foreach($entity->getEntityMetas() as $meta)
                                {
                                    $pattern = str_replace($meta->getName(), $meta->getValue(), $pattern);
                                }
                                $options['choices'][$pattern] = $entity->getId();
                            }
                        }
    
                    break;
                }
            } else {
                $class = HiddenType::class;
            }

            $filterForm->add($field->getName(), $class, $options);
        }

        return $filterForm;
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
        OptionRepository $optionRepository,
        RelationRepository $relationRepository
    ): Response
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

        $relations = $relationRepository->findBy(['model' => $model], ['position' => 'ASC']);
        $subviews = [];
        foreach($relations as $relation){
            $subviews[] = [
                'name' => $relation->getView()->getName(),
                'view_id' => $relation->getView()->getId(),
                'relation_id' => $relation->getId()
            ];
        }


        return $this->renderForm('entity/edit.html.twig', [
            'entity' => $entity,
            'form' => $form,
            'model' => $model,
            'view' => $view,
            'subviews' => $subviews
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

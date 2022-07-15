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

#[Route('/admin/model', priority: 1)]
class EntityController extends AbstractController
{
    #[Route('/{id}/view/{view_id}', name: 'app_entity_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request, 
        EntityRepository $entityRepository, 
        FormRepository $formRepository, 
        int $id, 
        IndexRepository $indexRepository, 
        int $view_id,
        FormFactoryInterface $formFactory,
        RelationRepository $relationRepository,
        $relation_id = '',
        $parent_id = ''
    ): Response
    {

        $session = $request->getSession();

        $model = $formRepository->find($id);
        $view = $indexRepository->find($view_id);
        $limit = !empty($view->getPagination()) ? $view->getPagination() : null;
        $page = $this->getCurrentPage($request, $model, $session, $relation_id);
        $orderBy = $this->getCurrentOrderBy($request, $model, $session, $view, $relation_id);
        $orderDirection = $this->getCurrentOrderDirection($request, $model, $session, $view, $relation_id);
        $order = [
            'order' => $orderBy,
            'direction' => $orderDirection ?? 'ASC'
        ];

        $filters = [];
        $activeFilters = false;
        if($relation_id){
            $relation = $relationRepository->find($relation_id);
            $filters[$relation->getMappedBy()->getName()] = $parent_id;
            $key = $relation->getId().'filters_'.$relation->getView()->getModel()->getId();
        } else {
            $key = $relation_id.'filters_'.$model->getId();
        }        
        if(!empty($_POST[$key])){
            $activeFilters = true;
            $filters = array_merge($filters, array_filter($_POST[$key]));
            $page = 1;
            $session->set($relation_id.'page_'.$model->getId(), $page);
        } elseif(!empty($session->get($key))) {
            $filters = array_merge($filters, array_filter($session->get($key)));
        } 
        $session->set($key, $filters);


        $count = $entityRepository->getCountByView($view, $filters);

        $pages = $limit ? ceil($count/$limit) : false;

        

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

        if(empty($relation)){
            $filterForm = $this->getFilterForm($entityRepository, $formRepository, $model, $formFactory, false, false);
            foreach($filters as $field => $value){
                if($filterForm->has($field)){
                    $filterForm->get($field)->setData($value);
                }
            }
            $filterForm->handleRequest($request);
            $resetForm = $this->getFilterForm($entityRepository, $formRepository, $model, $formFactory, true, false);
        } else {

            $filterForm = $this->getFilterForm($entityRepository, $formRepository, $relation->getView()->getModel(), $formFactory, false, $relation);            
            foreach($filters as $field => $value){
                if($filterForm->get($field)){
                    $filterForm->get($field)->setData($value);
                }
            }
            $filterForm->handleRequest($request);
            $resetForm = $this->getFilterForm($entityRepository, $formRepository, $relation->getView()->getModel(), $formFactory, true, $relation);
            if($resetForm->get($relation->getMappedBy()->getName())){
                $resetForm->get($relation->getMappedBy()->getName())->setData($parent_id);
            }
        }

        $template = !empty($relation) ? 'entity/view.html.twig' : 'entity/index.html.twig';
        return $this->render($template, [
            'entities' => $entities,
            'model' => $model,
            'view' => $view,
            'patterns' => $patterns,
            'externalEntities' => $externalEntities,
            'pages' => $pages,
            'currentPage' => $page,
            'total' => $count,
            'order' => $order,
            'activeFilters' => $activeFilters,
            'filterForm' => $filterForm->createView(),
            'resetForm' => $resetForm->createView(),
            'relation_id' => $relation_id
        ]);
    }

    private function getCurrentOrderBy($request, $model, $session, $view, $relation_id)
    {
        $orderBy = false;
        $key = $relation_id.'order_'.$model->getId();

        if(!empty($request->request->all()[$key])){
            $orderBy = $request->request->all()[$key];
        } elseif(!empty($session->get($key))) {
            $orderBy = $session->get($key);
        } elseif(!empty($view->getOrderBy())) {
            $orderBy = $view->getOrderBy()->getField()->getName();
        }      
        $session->set($key, $orderBy);   

        return $orderBy;
    }

    private function getCurrentOrderDirection($request, $model, $session, $view, $relation_id)
    {
        $orderDirection = false;
        if(!empty($request->request->all()[$relation_id.'direction_'.$model->getId()])){
            $orderDirection = $request->request->all()[$relation_id.'direction_'.$model->getId()];
        } elseif(!empty($session->get($relation_id.'direction_'.$model->getId()))) {
            $orderDirection = $session->get($relation_id.'direction_'.$model->getId());
        } elseif(!empty($view->getOrderDirection())) {
            $orderDirection = $view->getOrderDirection();
        }
        $session->set($relation_id.'direction_'.$model->getId(), $orderDirection);
        return $orderDirection;
    }

    private function getCurrentPage($request, $model, $session, $relation_id)
    {
        $page = !empty($request->request->all()[$relation_id.'page_'.$model->getId()]) 
        ? $request->request->all()[$relation_id.'page_'.$model->getId()] 
        : (
            !empty($session->get($relation_id.'page_'.$model->getId())) 
            ? $session->get($relation_id.'page_'.$model->getId()) 
            : 1
        );
        $session->set($relation_id.'page_'.$model->getId(), $page);
        return $page;
    }



    private function getFilterForm(
        EntityRepository $entityRepository, 
        FormRepository $formRepository, 
        $model, 
        FormFactoryInterface $formFactory, 
        $reset = false,
        $relation)
    {
        $entity = new Entity();
        $entity->setModel($model);

        $relation_id = $relation ? $relation->getId() : '';

        $filterForm = $formFactory->createNamed($relation_id.'filters_'.$model->getId(), EntityFormType::class, $entity, [
            'csrf_protection' => false
        ]);

        $fields = $model->getAttributes();

        $colClass = floor(12 / count($fields));

        foreach($fields as $field){

            $options = [
                'mapped' => false,
                'label' => $field->getLabel(),
                'required' => false,
                'attr' => [
                    'class' => 'col-md-'.$colClass
                ]
            ];
            if(!$reset){
                switch($field->getType()){
                    case 'textarea':
                    case 'text':
                        $class = TextType::class;
                    break;
                    case 'select':
                        
                        if($relation && $field->getName() == $relation->getMappedBy()->getName() && $filterForm->has($field->getName())){
                            $class = HiddenType::class;
                            break;
                        }

                        $class = EntityType::class;

                        if($field->getSelectEntity() == 'option' || empty($field->getSelectEntity())){
                            $options['class'] = Option::class;
                            $options['choices'] = $field->getOptions();
                            $options['choice_label'] = 'text';
                            $options['choice_value'] = function ($entity) {
                                if($entity instanceof Option){
                                    return $entity ? $entity->getId() : '';
                                } else {
                                    return intval($entity);
                                }
                            };
                        } else {
    
                            $model = $formRepository->find($field->getSelectEntity());
                            $entities = $entityRepository->findBy(['model' => $model]);
                            $class = ChoiceType::class;
                            $options['choices'] = ['' => ''];
                            foreach($entities as $row)
                            {
                                $pattern = $model->getDisplayPattern();
                                foreach($row->getEntityMetas() as $meta)
                                {
                                    $pattern = str_replace($meta->getName(), $meta->getValue(), $pattern);
                                }
                                $options['choices'][$pattern] = $row->getId();
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


    #[Route('/{id}/view/{view_id}/new', name: 'app_entity_new', methods: ['GET', 'POST'])]
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

    #[Route('/{form_id}/view/{view_id}/details/{id}', name: 'app_entity_edit', methods: ['GET', 'POST'])]
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
        RelationRepository $relationRepository,
        FormFactoryInterface $formFactory
    ): Response
    {
        $session = $request->getSession();

        $model = $formRepository->find($form_id);
        $view = $indexRepository->find($view_id);
        $form = $this->createForm(EntityFormType::class, $entity);
        foreach($entity->getEntityMetas() as $meta){

            $attribute = $attributeRepository->findOneBy([
                'name' => $meta->getName(),
                'form' => $model
            ]);
            if($form->has($meta->getName())){
                if($attribute && $attribute->getType() == 'select' && (empty($attribute->getSelectEntity()) || $attribute->getSelectEntity() == 'option')){
                    $option = $optionRepository->findOneBy(['id' => $meta->getValue()]);
                    $form->get($meta->getName())->setData($option);
                } else {
                    $form->get($meta->getName())->setData($meta->getValue());
                }
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

            $page = $this->getCurrentPage($request, $relation->getModel(), $session, $relation->getId());
            $orderBy = $this->getCurrentOrderBy($request, $relation->getModel(), $session, $relation->getView(), $relation->getId());
            $orderDirection = $this->getCurrentOrderDirection($request, $relation->getModel(), $session, $relation->getView(), $relation->getId());
            $order = [
                'order' => $orderBy,
                'direction' => $orderDirection ?? 'ASC'
            ];
            $subviews[] = [
                'name' => $relation->getView()->getName(),
                'view_id' => $relation->getView()->getId(),
                'relation_id' => $relation->getId(),
                'page' => $page,
                'currentPage' => $page,
                'order' => $order
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

    #[Route('/{form_id}/view/{view_id}/details/{id}/delete', name: 'app_entity_delete', methods: ['POST'])]
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

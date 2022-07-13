<?php

namespace App\Repository;

use App\Entity\Entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\ResultSetMapping;
use App\Entity\Index;
use App\Repository\FormRepository;

/**
 * @extends ServiceEntityRepository<Entity>
 *
 * @method Entity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entity[]    findAll()
 * @method Entity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, FormRepository $formRepository)
    {
        parent::__construct($registry, Entity::class);
        $this->formRepository = $formRepository;
    }

    public function add(Entity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Entity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // public function getDefaultOrderBy($view)
    // {
    //     $orderBy = !empty($view->getOrderBy()) ? ' ORDER BY '.$view->getOrderBy()->getField()->getName().' ' : false;
    //     $ordering = !empty($view->getOrderDirection()) ? $view->getOrderDirection() : false;
    //     if(!empty($orderBy)){
    //         $orderBy .= $ordering ? $ordering : 'ASC';
    //     }
    //     return !empty($orderBy) ? $orderBy : '';
    // }

    public function getCountByView($view, $filters)
    {
        // conditions
        $model = $view->getModel();
        $modelID = $model->getId();
        $columns = $view->getIndexColumns();
        $modelFields = $model->getAttributes();

        // Query
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('\App\Entity\Entity', 'e');
        $rsm->addFieldResult('e', 'TOTAL', 'id');
               
        $sql = "SELECT COUNT(e.`id`) AS TOTAL
            FROM `entity` e 
            LEFT JOIN (".$this->getMetadataJoinQuery($modelFields).") em 
            ON e.`id` = em.`entity_id` 
            WHERE e.`model_id` = ? ".$this->getFilters($filters, $modelFields).";";
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $modelID);
        $results = $query->getResult();
        $result = reset($results);
        return $result->getId();
    }

    public function getOrderBy($order)
    {
        $orderBy = !empty($order['order']) ? ' ORDER BY '.$order['order'].' ' : false;
        $ordering = !empty($order['direction']) ? $order['direction'] : false;
        if(!empty($orderBy)){
            $orderBy .= $ordering ? $ordering : 'ASC';
        }
        return !empty($orderBy) ? $orderBy : '';
    }



    public function findByView(Index $view, int $currentPage = 1, $order = false, $filters = [])
    {       
        // Default order of view
        $orderBy = $this->getOrderBy($order);
        
        // limit 
        $limit = !empty($view->getPagination()) ? $view->getPagination() : false;

        // offset
        $offset = $limit ? ($currentPage-1) * $limit : '';

        if(!empty($limit)){
            $limit = ' LIMIT '.$limit;
            $offset = ' OFFSET '.$offset;
        }

        // conditions
        $modelID = $view->getModel()->getId();

        // Query
        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('\App\Entity\Entity', 'e');
        $rsm->addFieldResult('e', 'id', 'id');
        $sql = $this->getQuery($view, $orderBy, $limit, $offset, $filters);
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $modelID);
        $entities = $query->getResult();

        return $entities;
    }

    public function getFilters($filters, $modelFields)
    {
        if(empty($filters)){
            return '';
        }
        $cond = "";
        foreach($modelFields as $field){
            if(array_key_exists($field->getName(), $filters)){
                $value = $filters[$field->getName()];
                switch($field->getType()){
                    case 'text':
                    case 'textarea':
                        $cond .= " AND ".$field->getName()." LIKE '%".$value."%'";
                    break;
                    break;
                    case 'select':
                        $cond .= " AND ".$field->getName()." = ".$value;
                    break;
                }
            }
        }
        return $cond;
    }


    public function getQuery(Index $view, $orderBy, $limit, $offset, $filters)
    {   
        $model = $view->getModel();
        $columns = $view->getIndexColumns();
        $modelFields = $model->getAttributes();
               
        return "SELECT e.`id`, ".$this->getMetadataColumns($columns, $view)." 
            FROM `entity` e 
            LEFT JOIN (".$this->getMetadataJoinQuery($modelFields).") em 
            ON e.`id` = em.`entity_id` 
            WHERE e.`model_id` = ? ".$this->getFilters($filters, $modelFields)." ".$orderBy." ".$limit." ".$offset.";";

    }

    public function isExternalField($column, $view)
    {
        return $column->getField()->getForm()->getId() !== $view->getModel()->getId();
    }

    public function getRelationalField($column, $view)
    {
        $modelID = $column->getField()->getForm()->getId();   
        $relationalField = $view->getModel()->getAttributes()->filter(function($attribute) use ($modelID) {
            return $attribute->getSelectEntity() == $modelID;
        })->first();
        return $relationalField;
    }

    public function getConcatQueryForExternalField($column, $relationalField)
    {
        $model = $this->formRepository->find($column->getField()->getSelectEntity());
        $pattern = $model->getDisplayPattern();
        
        $concat = [];
        foreach($model->getAttributes() as $attribute){
            $sql = "( 
                SELECT `entity_meta`.`value` 
                FROM `entity_meta` 
                WHERE `entity_meta`.`name` = '".$attribute->getName()."' 
                AND `entity_meta`.`entity_id` = (
                    SELECT `entity_meta`.`value` 
                    FROM `entity_meta` 
                    WHERE `entity_meta`.`name` = '".$column->getField()->getName()."' 
                    AND `entity_meta`.`entity_id` = em.".$relationalField->getName()." 
                )
            )";
            $pattern = str_replace(
                $attribute->getName(), 
                "$$".$sql."$$", 
                $pattern
            );
        }
        $concat = implode("%%%", array_filter(explode('$$', $pattern)));
        $concat = preg_replace("/%%%(.*)%%%/",',"${1}",', $concat);

        $concat = "(SELECT CONCAT(".$concat.")) AS ".$column->getField()->getName();
        return $concat;

    }

    public function getMetadataColumns($columns, $view)
    {   
        $cols = [];
        foreach($columns as $column){
            // Basé sur un champ externe
            if($this->isExternalField($column, $view)){
                $relationalField = $this->getRelationalField($column, $view);
                //  direct (1 relation)
                if(empty($column->getField()->getSelectEntity())){    
                    $cols[] = "(
                        SELECT `entity_meta`.`value` 
                        FROM `entity_meta` 
                        WHERE `entity_meta`.`name` = '".$column->getField()->getName()."' 
                        AND `entity_meta`.`entity_id` = em.".$relationalField->getName()."
                    ) AS ".$column->getField()->getName();
                //  indirect (2+ relation)
                } else {
                    $cols[] = $this->getConcatQueryForExternalField($column, $relationalField);
                }
            } else {
                // Basé sur un champ classique 
                if($column->getField()->getType() !== 'select'){
                    $cols[] = "em.".$column->getField()->getName();
                // Basé sur un select > option
                } else if(empty($column->getField()->getSelectEntity()) || $column->getField()->getSelectEntity() == 'option'){ 
                    $cols[] = "(SELECT `option`.`text` FROM `option` WHERE `option`.`id` = em.`".$column->getField()->getName()."`) AS ".$column->getField()->getName()."";
                }
            }

        }
        return implode(', ', $cols);
    }

    public function getMetadataJoinQuery($modelFields)
    {
        $cols = [];
        foreach($modelFields as $field){
            $cols[] = "MAX(CASE WHEN (name='".$field->getName()."') THEN value ELSE NULL END) AS '".$field->getName()."'";
        }
        return "SELECT entity_id, ".implode(', ', $cols)." FROM `entity_meta` group by entity_id";
    }




}

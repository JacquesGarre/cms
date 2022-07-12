<?php

namespace App\Repository;

use App\Entity\Entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\ResultSetMapping;
use App\Entity\Index;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entity::class);
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

    public function getDefaultOrderBy($view)
    {
        $orderBy = !empty($view->getOrderBy()) ? ' ORDER BY '.$view->getOrderBy()->getField()->getName().' ' : false;
        $ordering = !empty($view->getOrderDirection()) ? $view->getOrderDirection() : false;
        if(!empty($orderBy)){
            $orderBy .= $ordering ? $ordering : 'ASC';
        }
        return !empty($orderBy) ? $orderBy : '';
    }


    public function findByView(Index $view, int $currentPage = 1)
    {   
        // Default order of view
        $orderBy = $this->getDefaultOrderBy($view);

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
        $sql = $this->getQuery($view, $orderBy, $limit, $offset);
        dd($sql);
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $modelID);
        $entities = $query->getResult();

        return $entities;
    }


    public function getQuery(Index $view, $orderBy, $limit, $offset)
    {   
        $columns = $view->getIndexColumns();
               
        return "SELECT e.`id`, ".$this->getMetadataColumns($columns, $view)." 
            FROM `entity` e 
            LEFT JOIN (".$this->getMetadataJoinQuery($columns).") em 
            ON e.`id` = em.`entity_id` 
            WHERE e.`model_id` = ? ".$orderBy." ".$limit." ".$offset.";";

    }

    public function getMetadataColumns($columns, $view)
    {   
        
        $cols = [];
        foreach($columns as $column){

            // Basé sur un champ externe direct (1 relation)
            if($column->getField()->getForm()->getId() !== $view->getModel()->getId()){

                $modelID = $column->getField()->getForm()->getId();
                $relationalField = $view->getModel()->getAttributes()->filter(function($attribute) use ($modelID) {
                    return $attribute->getSelectEntity() == $modelID;
                })->first();


                $attributes = $column->getField()->getForm()->getAttributes();
                $pattern = $column->getField()->getForm()->getDisplayPattern();
                $field = "";
                foreach($attributes as $attribute){
                    if(strpos($pattern, $attribute->getName()) !== FALSE){
                        $field = $attribute->getName();
                        break;
                    }
                }

                $cols[] = "(
                    SELECT `entity_meta`.`value` 
                    FROM `entity_meta` 
                    WHERE `entity_meta`.`name` = '".$column->getField()->getName()."' 
                    AND `entity_meta`.`entity_id` = em.".$relationalField->getName()."
                ) AS ".$column->getField()->getName();

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



        // Nom	  Type	   Age	   Prénom du proprio	 Nom du proprio	      Ville du proprio

        // $query = "em.name,   -- Basé sur un champ classique 
        //     (SELECT `option`.`text` FROM `option` WHERE `option`.`id` = em.`type`) AS type, -- Basé sur un select > option
        //     em.age,   -- Basé sur un champ classique 
        //     (
        //         SELECT `entity_meta`.`value` 
        //         FROM `entity_meta` 
        //         WHERE `entity_meta`.`name` = 'firstname' 
        //         AND `entity_meta`.`entity_id` = em.owner 
        //     ) AS firstname,   -- Basé sur un select > entity
        //     (
        //         SELECT `entity_meta`.`value` 
        //         FROM `entity_meta` 
        //         WHERE `entity_meta`.`name` = 'lastname' 
        //         AND `entity_meta`.`entity_id` = em.owner 
        //     ) AS lastname,   -- Basé sur un select > entity
        //     (
        //         SELECT `entity_meta`.`value` 
        //         FROM `entity_meta` 
        //         WHERE `entity_meta`.`name` = 'name' 
        //         AND `entity_meta`.`entity_id` = (
        //             SELECT `entity_meta`.`value` 
        //             FROM `entity_meta` 
        //             WHERE `entity_meta`.`name` = 'city' 
        //             AND `entity_meta`.`entity_id` = em.owner 
        //         )
        //     ) AS city -- Basé sur un champ externe";

        return implode(', ', $cols);
    }

    public function getMetadataJoinQuery($columns)
    {
        $cols = [];
        foreach($columns as $column){
            $cols[] = "MAX(CASE WHEN (name='".$column->getField()->getName()."') THEN value ELSE NULL END) AS '".$column->getField()->getName()."'";
        }
        return "SELECT entity_id, ".implode(', ', $cols)." FROM `entity_meta` group by entity_id";
    }




}

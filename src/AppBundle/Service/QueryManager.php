<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Service\PaginatorManager;

class QueryManager
{
    protected $em;
    protected $paginatorManager;

    public function __construct(EntityManagerInterface $em, PaginatorManager $paginatorManager){
        $this->setEntityManager($em);
        $this->paginatorManager = $paginatorManager;
    }

    public function setEntityManager($em){
        return $this->em = $em;
    } 

    public function getEntityManager(){
        return $this->em;
    }
    
    public function setPaginatorManager($paginatorManager){
        return $this->paginatorManager = $paginatorManager;
    } 

    public function getPaginatorManager(){
        return $this->paginatorManager;
    } 

    public function getOffsetLimit(array $criteria_pagination = null){
        // Get pagination elements
        $page = $criteria_pagination['page'];
        $count = $criteria_pagination['count'];
        // Process pagination variables and set the limit and offset
        $init_read = false;
        if($page < 1 || $count < 1){
            $init_read = true;
        }
        $limit = ($init_read) ? 1 :  $count;
        $offset = ($init_read) ? 1 : ($count * $page) - $count; // $count($page - 1)
        $init_read = false;
        return array('limit'=>$limit, 'offset'=>$offset);
    }

    public function buildQueryByCriterias(array $criteria = null){        
        // Get the entity class for setting repository by entity manager
        $entityClass = $criteria['entity_class']['class'];
        
        // Get the entity Alias
        $entityAlias = $criteria['entity_class']['alias'];

        // Build select query
        $querySelect_all_flag = false;
        $querySelect = "select";
        foreach ($criteria['criteria-select'] as $criteria_select) {
            if($criteria_select == '*'){
                $querySelect_all_flag = true;
                $querySelect .= " " . $entityAlias;
            }
            else if(stristr($criteria_select, "(") != false){
                /* example
                str = "COUNT(id)";
                Match 1
                Full match	0-9	`COUNT(id)`
                Group 1.	0-6	`COUNT(`
                Group 2.	6-8	`id`
                Group 3.	8-9	`)`
                */
                $pattern = '/(.{1,}\()(.{1,})(\))/i';
                $replacement = '${1} ' . $entityAlias . '.$2 $3';
                $res = preg_replace($pattern, $replacement, $criteria_select);
                $querySelect .= 
                             " "
                             . $res
                             . ",";
            }
            else{
                $querySelect .= 
                             " "
                             . $entityAlias
                             . "."
                             . $criteria_select
                             . ",";
            }
        }
        if($querySelect_all_flag == false){
            $querySelect = substr($querySelect, 0, -1); // remove the comma at the end of string.
        }

        // Build from query
        $queryFrom = "from";
        $queryFrom .= " " . $criteria['criteria-from']['class']  . " " . $criteria['criteria-from']['alias'];

        // Build and set where criteria : criteria-where
        $queryWhere = "where";
        foreach ($criteria['criteria-where'] as $criteria_where) {
            if($criteria_where['criterias-condition'] == null){
                $queryWhere .= " ( ";
                for ($i=0; $i < count($criteria_where['criterias']); $i++) {
                    $queryWhere .= 
                        $entityAlias
                        . "." 
                        . $criteria_where['criterias'][$i]['column']['name'] 
                        . " "
                        . $criteria_where['criterias'][$i]['operator']['affectation']
                        . " "
                        . "'" 
                        . $criteria_where['criterias'][$i]['column']['value'] . "'";
                }
            }
            else{
                $queryWhere .= $criteria_where['criterias-condition'] . " ( ";
                for ($i=0; $i < count($criteria_where['criterias']); $i++) {
                    if($criteria_where['criterias'][$i]['operator']['condition'] != null){
                        $queryWhere .= 
                                    " "
                                    . $criteria_where['criterias'][$i]['operator']['condition']
                                    . " ";
                    }
                    $queryWhere .= 
                            $entityAlias
                            . "." 
                            . $criteria_where['criterias'][$i]['column']['name'] 
                            . " "
                            . $criteria_where['criterias'][$i]['operator']['affectation']
                            . " "
                            . "'" 
                            . $criteria_where['criterias'][$i]['column']['value'] . "'";
                }
            }
            $queryWhere .= " ) ";
        }

        // Build order by query
        $queryOrderby = "order by";   
        foreach ($criteria['criteria-orderby'] as $column => $orderby) {
            $queryOrderby .= " " . $entityAlias . "." . $column . " " . $orderby . ",";
        }
        $queryOrderby = substr($queryOrderby, 0, -1); // remove the comma at the end of string.

        // Build query
        $query = $querySelect . " ". $queryFrom . " " . $queryWhere . $queryOrderby;

        /**** Using pure DQL without setParameters function ****/
        $query_em = $this->getEntityManager()->createQuery($query);

        // Return query built
        return $query_em;
    }

    public function executeQuery($qb){
        $resutl = $qb->getResult();
        return $resutl;
    }

    public function findByCriterias(array $criteria = null){
        $qb = $this->buildQueryByCriterias($criteria);
        $PaginatorPageCount = $this->getPaginatorManager()->getPaginatorPageCount();
        $offsetLimit = $this->getOffsetLimit($PaginatorPageCount);


        //$qb->orderBy('c.id', 'DESC');

        $result = $this->executeQuery($qb->setFirstResult($offsetLimit['offset'])->setMaxResults($offsetLimit['limit']));
        if($criteria['pagination']['enabled']){
            // Get Paginator Attributes
            $paginator_entity = $this->getPaginatorManager()->getPaginatorAttributes(
                $PaginatorPageCount,
                $categories_max_by_criteria = $this->maxEntitiesByCriterias($criteria), // Get maximum of entity regarding criteria
                $route = $criteria['pagination']['route'], // Get route pagination
                $entityNameHandled = $criteria['pagination']['entity-name'] // Get Entity name
            );
            return array('result' => $result, 'paginator' => $paginator_entity);
        }
        return array('result' => $result);
    }

    public function maxEntitiesByCriterias(array $criteria = null){
        $criteria['criteria-select'] = array('count(id)');
        $qb = $this->buildQueryByCriterias($criteria);
        $result = $this->executeQuery($qb);
        return $result[0]['1'];
    }

    public function onDeleteByCriterias($criterias, $batch_size = 10){
        $qb = $this->buildQueryByCriterias($criterias);
        $qb->setMaxResults( $batch_size );
        $flag_delete_less_one_entity = false; // Return this value (for example to a controller)
        $batch_has_alive = true;
        while ($batch_has_alive)
        {
            $batch_has_alive = false;
            $results = $this->executeQuery($qb);
            $batch_has_alive = ($results != null) ? true : false ;
            foreach ($results as $result) {
                $em_remove = $this->getEntityManager()->remove($result);
                $em_flush = $this->getEntityManager()->flush(); // Executes all deletions.
                $flag_delete_less_one_entity = true;
            }
        }
        return $flag_delete_less_one_entity;
    }
}
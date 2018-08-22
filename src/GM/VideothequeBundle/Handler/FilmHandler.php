<?php

namespace GM\VideothequeBundle\Handler;

use GM\VideothequeBundle\Entity\Film;

/**
 * Photo Handler.
 *
 */
class FilmHandler
{
    protected $criterias;

    public function __construct(){
        $this->criterias = array( // Get Categories with rich embeded criteria-v3
                'pagination' => array(
                    'enabled' => true,
                    'entity-name' => 'Film',
                    'route' => array('route_name' => $this->getRoute('index'), 'params' => null), // Get route pagination
                ),
                'entity_class' => array(
                    'class' => Film::class,
                    'alias' => 'f',
                ),
                'repository' => array(
                    'class' => 'GMVideothequeBundle:Film',
                ),
                'criteria-select' => array('*'),
                'criteria-from' => array(
                    'class' => Film::class,
                    'alias' => 'f',
                ),
                'criteria-orderby' => array('id' => 'ASC'),
                'criteria-where' => null
        );
    }

    public function getCriterias($criteria_update  = null){
        return $this->criterias;
    }

    public function getRoute($template = 'index'){
        $listRoutes = array(
            'new' => 'film_new',
            'index' => 'film_index',
            'show' => 'film_show',
            'edit' => 'film_edit',
            'rest_index' => 'rest_film_index',
        );
        return $listRoutes[$template];
    }

    public function getTwig($template = 'index'){
        $listTemplates = array(
            'new' => 'GMVideothequeBundle:film:new.html.twig',
            'index' => 'GMVideothequeBundle:film:index.html.twig',
            'show' => 'GMVideothequeBundle:film:show.html.twig',
            'edit' => 'GMVideothequeBundle:film:edit.html.twig',
        );
        return $listTemplates[$template];
    }
}
<?php

namespace GM\VideothequeBundle\Controller;

use GM\VideothequeBundle\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use GM\VideothequeBundle\Form\CategorieType;

/**
 * Categorie controller.
 *
 */
class RestCategorieController extends Controller
{
    public function indexAction(Request $request)
    {
        $this->securityGuardianAccess(); // Calling of security Guardian
        $criterias = $this->get('categorie_handler')->getCriterias();
        $criterias['pagination']['route']['route_name'] = ( $this->get('categorie_handler')->getRoute('rest_index') );
        $criterias['criteria-where'] = $this->getBaseCriterias_categorie();

        $searchByCriterias = $this->get('search_engine_manager')->getSearchByCriteriasWhere();
        if($searchByCriterias != null){
            $criterias['criteria-where'][]= $searchByCriterias;
        }
        $orderByCriterias = $this->get('search_engine_manager')->getOrderByCriterias();
        $criterias['criteria-orderby'] = $orderByCriterias;

        $categories = $this->get('query_manager')->findByCriterias( $criterias ); // Get query's result
        $delete_all_categories_url = $this->generateUrl('rest_categorie_delete_all');

        return new JsonResponse(array('data' => array('categories' => $categories, 'delete_all_categories_url' => $delete_all_categories_url), 'msg' => 'OK', 'status' => 200));
    
    }

    public function showAction(Request $request, Categorie $categorie,  $id)
    {
        $this->securityGuardianAccess(); // Calling of security Guardian
        if($categorie->isOwner($this->getUser()->getId() )){ // Verify if request is allowed for the current user
            $FilmsParCategorie = $this->FilmsParCategorie($id);
        }
        else{
            $msgGen = $this->get('message_generator')->Msg_Action_FAIL();
            return new JsonResponse(array('data' => null, 'msg' => $msgGen, 'status' => 404));
        }
        $delete_categorie_url = $this->generateUrl('rest_categorie_delete', array('id' => $id));
        
        //return new Response();
        return new JsonResponse(array(
            'data' => array(
                'categorie' => $categorie, 'delete_categorie' => $delete_categorie_url, 
                'films' => $FilmsParCategorie
            ),
                'msg' => 'OK', 
                'status' => 200
            )
        );
    }

    public function deleteAction(Request $request, $id, Categorie $categorie)
    {
        $criterias = $this->get('categorie_handler')->getCriterias();
        $criterias['pagination']['enabled'] = false;
        $criterias['criteria-where'] = $this->getBaseCriterias_categorie();
        $criterias['criteria-where'][] = 
                array(
                    'criterias' => array(
                        array(
                                'column' => array(
                                'name' => 'id',
                                'value' => $id
                            ),
                            'operator' => array(
                                'affectation' => '=',
                                'condition' => null
                            ),
                        ),
                    ),
                    'criterias-condition' => 'and'
                );
        $delation_ok = $this->get('query_manager')->onDeleteByCriterias($criterias, $batch_size = 5);
        if($delation_ok){
            $msgGen = $this->get('message_generator')->Msg_DeleteDB_OK();
            return new JsonResponse(array('data' => null, 'msg' => $msgGen, 'status' => 200));
            
        }
        else{
            $msgGen = $this->get('message_generator')->Msg_DeleteDB_NONE();
            return new JsonResponse(array('data' => null, 'msg' => $msgGen, 'status' => 404));
        }
    }

    public function delete_allAction(Request $request)
    {
        $criterias = $this->get('categorie_handler')->getCriterias();
        $criteria['pagination']['enabled'] = false;
        $criterias['criteria-where'] = $this->getBaseCriterias_categorie();
        $delation_ok = $this->get('query_manager')->onDeleteByCriterias($criterias, $batch_size = 20);
        if($delation_ok){
            $msgGen = $this->get('message_generator')->Msg_DeleteDB_OK();
            return new JsonResponse(array('data' => null, 'msg' => $msgGen, 'status' => 200));
        }
        else{
            $msgGen = $this->get('message_generator')->Msg_DeleteDB_NONE();
            return new JsonResponse(array('data' => null, 'msg' => $msgGen, 'status' => 404));
        }
    }

    public function securityGuardianAccess($role = 'ROLE_USER'){
        $this->denyAccessUnlessGranted($role, null, 'Unable to access this page!');
    }

    public function getBaseCriterias_categorie()
    {
        $criteria = array( 
            array(
                'criterias' => array(
                    array(
                            'column' => array(
                            'name' => 'owner',
                            'value' => $this->getUser()->getId()
                        ),
                        'operator' => array(
                            'affectation' => '=',
                            'condition' => null
                        ),
                    ),
                ),
                'criterias-condition' => null
            )
        );
        return $criteria;
    }

    public function getBaseCriterias_film()
    {
        $criteria = array( 
            array(
                'criterias' => array(
                    array(
                            'column' => array(
                            'name' => 'owner',
                            'value' => $this->getUser()->getId()
                        ),
                        'operator' => array(
                            'affectation' => '=',
                            'condition' => null
                        ),
                    ),
                ),
                'criterias-condition' => null
            )
        );
        return $criteria;
    }
    
    public function showFilmParCategorieAction(Request $request, Categorie $categorie, $id, $page, $count)
    {
        $this->securityGuardianAccess(); // Calling of security Guardian
        if($categorie->isOwner($this->getUser()->getId() )){ // Verify if request is allowed for the current user
            $FilmsParCategorie = $this->FilmsParCategorie($id);
        }
        else{
            $msgGen = $this->get('message_generator')->Msg_Action_FAIL();
            return new JsonResponse(array('data' => null, 'msg' => $msgGen, 'status' => 404));
        }
        $delete_categorie_url = $this->generateUrl('rest_categorie_delete', array('id' => $id));
        return new JsonResponse(array(
            'data' => array(
                'categorie' => $categorie, 'delete_categorie' => $delete_categorie_url, 
                'films' => $FilmsParCategorie
            ),
                'msg' => 'OK', 
                'status' => 200
            )
        );
    }

    public function FilmsParCategorie($categorieID)
    {
        $criterias = $this->get('film_handler')->getCriterias();
            $criterias['pagination']['route'] = array(
                'route_name' => $this->get('categorie_handler')->getRoute('rest_show_film_par_categorie'), //rest_show_film_par_categorie
                'params' => array('id' => $categorieID)
            );
            $criterias['criteria-where'] = $this->getBaseCriterias_film();
            $searchByCriterias = $this->get('search_engine_manager')->getSearchByCriteriasWhere();
            if($searchByCriterias != null){
                $criterias['criteria-where'][]= $searchByCriterias;
            }
            $orderByCriterias = $this->get('search_engine_manager')->getOrderByCriterias();
            $criterias['criteria-orderby'] = $orderByCriterias;
    
            $criterias['criteria-where'][] = 
                array(
                    'criterias' => array(
                        array(
                                'column' => array(
                                'name' => 'categorie',
                                'value' => $categorieID
                            ),
                            'operator' => array(
                                'affectation' => '=',
                                'condition' => null
                            ),
                        ),
                    ),
                    'criterias-condition' => 'and'
                );    
        $films = $this->get('query_manager')->findByCriterias( $criterias ); // Get query's result
        //dump($criterias);
        return $films;
    }
}

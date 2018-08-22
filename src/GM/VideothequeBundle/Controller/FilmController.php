<?php

namespace GM\VideothequeBundle\Controller;

use GM\VideothequeBundle\Entity\Film;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use GM\VideothequeBundle\Form\FilmType;

/**
 * Film controller.
 *
 */
class FilmController extends Controller
{    
    public function indexAction(Request $request)
    {
        $this->securityGuardianAccess(); // Calling of security Guardian
        $criterias = $this->get('film_handler')->getCriterias();
        $criterias['criteria-where'] = $this->getBaseCriterias_film();
        $criterias['pagination']['route']['route_name'] = ( $this->get('film_handler')->getRoute('rest_index') ); // Using API to call json data
        
        $films = $this->get('query_manager')->findByCriterias( $criterias ); // Get query's result
        
        return $this->render(
            $this->get('film_handler')->getTwig('index'),
            array(
                'films' => $films
            ));
    }

    public function newAction(Request $request)
    {
        $this->securityGuardianAccess();
        $film = new Film($this->getUser());
        $form = $this->get('form_manager')->createForm(FilmType::class, $film, array('owner_user_id' => $this->getUser()->getId()), 'create');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $resultInsertData = $em->flush();
            $msgGen = $this->get('message_generator')->Msg_InsertionDB_OK();
            $request->getSession()->getFlashBag()->add("success", $msgGen);
            return $this->redirectToRoute('film_show', array('id' => $film->getId()));
        }
        return $this->render(
            $this->get('film_handler')->getTwig('new'), 
            array(
                'form' => $form->createView()
        ));
    }

    public function showAction(Request $request, Film $film,  $id)
    {
        $this->securityGuardianAccess(); // Calling of security Guardian
        if($film->isOwner($this->getUser()->getId() )){ // Verify if request is allowed for the current user
        }
        else{
            $msgGen = $this->get('message_generator')->Msg_Action_FAIL();
            $request->getSession()->getFlashBag()->add("warning", $msgGen);
            return $this->redirectToRoute('film_index');
        }
        return $this->render(
            $this->get('film_handler')->getTwig('show'), 
            array(
                'film' => $film, 
                'delete_form' => $this->getDeleteFormById($id)->createView()
            ));
    }

    public function editAction(Request $request, Film $film, $id)
    {
        $this->securityGuardianAccess();
        if($film->isOwner($this->getUser()->getId()) ){

            $editForm = $this->get('form_manager')->createForm(FilmType::class, $film, array('owner_user_id' => $this->getUser()->getId()), 'update');
            $editForm->handleRequest($request);
            if ($editForm->isSubmitted() && $editForm->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($editForm->getData());
                $resultUpdateData = $em->flush();
                $msgGen = $this->get('message_generator')->Msg_UpdateDB_OK();
                $request->getSession()->getFlashBag()->add("success", $msgGen);
                return $this->redirectToRoute('film_edit', array('id' => $film->getId()));
            }
            return $this->render(
                $this->get('film_handler')->getTwig('edit'), 
                array(
                    'film' => $film, 
                    'edit_form' => $editForm->createView(), 
                    'delete_form' => $this->getDeleteFormById($id)->createView()
                ));
        }
        else{
            $msgGen = $this->get('message_generator')->Msg_Action_FAIL();
            $request->getSession()->getFlashBag()->add("warning", $msgGen);
            return $this->redirectToRoute('film_index');
        }
    }

    public function deleteAction(Request $request, $id, Film $film)
    {
        $criterias = $this->get('film_handler')->getCriterias();
        $criterias['pagination']['enabled'] = false;
        $criterias['criteria-where'] = $this->getBaseCriterias_film();
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
            $request->getSession()->getFlashBag()->add("success", $msgGen);
            
        }
        else{
            $msgGen = $this->get('message_generator')->Msg_DeleteDB_NONE();
            $request->getSession()->getFlashBag()->add("warning", $msgGen);
        }
        return $this->redirectToRoute('film_index');
    }

    public function delete_allAction(Request $request)
    {
        $criterias = $this->get('film_handler')->getCriterias();
        $criteria['pagination']['enabled'] = false;
        $criterias['criteria-where'] = $this->getBaseCriterias_film();
        $delation_ok = $this->get('query_manager')->onDeleteByCriterias($criterias, $batch_size = 20);
        if($delation_ok){
            $msgGen = $this->get('message_generator')->Msg_DeleteDB_OK();
            $request->getSession()->getFlashBag()->add("success", $msgGen);
        }
        else{
            $msgGen = $this->get('message_generator')->Msg_DeleteDB_NONE();
            $request->getSession()->getFlashBag()->add("warning", $msgGen);
        }
        return $this->redirectToRoute('film_index');
    }

    public function getDeleteFormById($id){
        $option_delete_form = array('action' => $this->generateUrl('film_delete', array('id' => $id)), 'method' => 'DELETE');
        $delete_form = $this->get('form_manager')->createForm(FormType::class, new film($this->getUser()), $option_delete_form, 'delete');
        return $delete_form;
    }

    public function securityGuardianAccess($role = 'ROLE_USER'){
        $this->denyAccessUnlessGranted($role, null, 'Unable to access this page!');
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

}
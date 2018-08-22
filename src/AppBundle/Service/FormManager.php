<?php

namespace AppBundle\Service;

use Symfony\Component\Form\FormFactoryInterface;


/**
 * FormManager.
 * 
 */
class FormManager
{
    protected $formFactory;
    protected $optionsForm;
    protected $createform;
    protected $editform;
    protected $deleteform;
    
    public function __construct(FormFactoryInterface $formFactory){
        $this->setFormFactory($formFactory);
        $this->setOptionForm(array());
    }

    public function getFormFactory(){
        return $this->formFactory;
    }

    public function setFormFactory($formFactory){
        return $this->formFactory = $formFactory;
    }

    public function getCreateForm(){
        return $this->createform;
    }

    public function setCreateForm($createform){
        $this->createform = $createform;
    }

    public function getEditForm(){
        return $this->editform;
    }

    public function setEditForm($editform){
        $this->editform = $editform;
    }

    public function getDeleteForm(){
        return $this->deleteform;
    }

    public function setDeleteForm($deleteform){
        $this->deleteform = $deleteform;
    }

    public function getOptionForm(){
        return $this->optionForm;
    }

    public function setOptionForm($optionForm){
        $this->optionForm = $optionForm;
    }

    public function createForm($type, $data = null, array $options = array(), $formRole)
    {
        $this->setOptionForm($options);
        switch ($formRole) {
            case 'create':
                $this->setCreateForm($this->getFormFactory()->create($type, $data, $this->getOptionForm()));
                return $this->getCreateForm();

            case 'delete':
                $this->setDeleteForm($this->getFormFactory()->create($type, $data, $this->getOptionForm()));
                return $this->getDeleteForm();

            case 'edit':
                $this->setEditForm($this->getFormFactory()->create($type, $data, $this->getOptionForm()));
                return $this->getEditForm();
            
            default:
                $this->setCreateForm($this->getFormFactory()->create($type, $data, $this->getOptionForm()));
                return $this->getCreateForm();
        }
    }
}
<?php

class Administrador_Form_Courses extends Zend_Form {

    public function init() {
        $this->setAction("")
             ->setMethod('post');
        
        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
             ->setAttribs(array("class" => "required", "data-error-type" => "inline"))
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->setRequired(true)
                    ->setAttribs(array("class" => "required editor", "data-error-type" => "inline"))
                    //->addFilter('StripTags')
                    ->addFilter('StringTrim');
        
        $value = new Zend_Form_Element_Text('value');
        $value->setRequired(true)
              ->setAttribs(array("class" => "required", "data-error-type" => "inline"))
              ->addFilter('StripTags')
              ->addFilter('StringTrim');
        
        $workload = new Zend_Form_Element_Text('workload');
        $workload->setRequired(true)
                 ->setAttribs(array("class" => "required", "data-error-type" => "inline"))
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Adicionar Curso");
        
        $this->addElements(array($name, $description, $value, $workload, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }

}


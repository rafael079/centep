<?php

class Administrador_Form_Disciplines extends Zend_Form {

    public function init() {
        $this->setAction("")
             ->setMethod('post');
        
        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
             ->setAttribs(array("class" => "required", "data-error-type" => "inline"))
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $course = new Zend_Form_Element_Select('course');
        $course->setRequired(true)
               ->setAttribs(array("class" => "required search", "data-error-type" => "inline", "data-placeholder" => "Selecione um Curso"))
               ->addFilter('StripTags')
               ->addFilter('StringTrim');
        $course->setRegisterInArrayValidator(false);
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->setRequired(true)
                    ->setAttribs(array("class" => "required editor", "data-error-type" => "inline"))
                    //->addFilter('StripTags')
                    ->addFilter('StringTrim');
        
        $workload = new Zend_Form_Element_Text('workload');
        $workload->setRequired(true)
                 ->setAttribs(array("class" => "required", "data-error-type" => "inline"))
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Adicionar Disciplina");
        
        $this->addElements(array($name, $course, $description, $workload, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }

}


<?php

class Administrador_Form_Teachers extends Zend_Form {

    public function init() {
        $this->setMethod('post');
        
        $courses = new Zend_Form_Element_Select('courses');
        $courses->setRequired(true)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("data-placeholder", "Escolha um Curso")
                ->setAttrib("class", "required search")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $courses->setRegisterInArrayValidator(false);
       
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Próximo Passo");
        
        $this->addElements(array($courses, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            ));
    }

}


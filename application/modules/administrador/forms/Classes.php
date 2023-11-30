<?php

class Administrador_Form_Classes extends Zend_Form {

    public function init() {
        $this->setAction('')
                ->setMethod('post');

        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
                ->setAttribs(array("class" => "required", "data-error-type" => "inline"))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $this->setMethod("post");

        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Criar Turma");

        $this->addElements(array($name, $submit));

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }

}


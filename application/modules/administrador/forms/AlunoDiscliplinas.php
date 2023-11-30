<?php

class Administrador_Form_AlunoDiscliplinas extends Zend_Form {

    public function init() {
        $this->setAction('')
             ->setMethod('post');
        
        $disciplines_1 = new Zend_Form_Element_Multiselect('disciplines_1');
        $disciplines_1->setRequired(true)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required dualselects")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $disciplines_1->setRegisterInArrayValidator(false);
        
        $disciplines_2 = new Zend_Form_Element_Multiselect('disciplines_2');
        $disciplines_2->setRequired(true)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required dualselects")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $disciplines_2->setRegisterInArrayValidator(false);
        
        $disciplines_3 = new Zend_Form_Element_Multiselect('disciplines_3');
        $disciplines_3->setRequired(true)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required dualselects")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $disciplines_3->setRegisterInArrayValidator(false);
        
        $disciplines_4 = new Zend_Form_Element_Multiselect('disciplines_4');
        $disciplines_4->setRequired(true)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required dualselects")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $disciplines_4->setRegisterInArrayValidator(false);

        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Adicionar Disciplina");

        $this->addElements(array($disciplines_1, $disciplines_2, $disciplines_3, $disciplines_4, $submit));

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }

}


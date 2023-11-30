<?php

class Administrador_Form_Boletos extends Zend_Form {

    public function init() {
        $this->setAction('')
             ->setMethod('post');
        
        $value = new Zend_Form_Element_Text('value');
        $value->setRequired(true)
                ->setAttribs(array("class" => "required", "data-error-type" => "inline", "placeholder" => "Ex: 100,99"))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $this->setMethod("post");
        
        $expiration = new Zend_Form_Element_Text('expiration');
        $expiration->setRequired(true)
                ->setAttribs(array("class" => "hasDatepicker"))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $this->setMethod("post");
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Gerar Boleto");

        $this->addElements(array($value, $expiration, $submit));

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }

}


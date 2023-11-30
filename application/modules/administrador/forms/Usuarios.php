<?php

class Administrador_Form_Usuarios extends Zend_Form {

    public function init() {
        $this->setAction("")
             ->setMethod('post');
        
        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
             ->setAttrib("data-error-type", "inline")
             ->setAttrib("class", "required")
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $mail = new Zend_Form_Element_Text('mail');
        $mail->setRequired(true)
             ->setAttrib("data-error-type", "inline")
             ->setAttrib("class", "required")
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $username = new Zend_Form_Element_Text('username');
        $username->setRequired(true)
                 ->setAttrib("data-error-type", "inline")
                 ->setAttrib("class", "required")
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim');
        
        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(true)
                 ->setAttrib("data-error-type", "inline")
                 ->setAttrib("class", "required")
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim');
        
        $group = new Zend_Form_Element_Select('group');
        $group->setRequired(true)
              ->setAttrib("data-error-type", "inline")
              ->setAttrib("class", "required")
              ->addFilter('StripTags')
              ->addFilter('StringTrim');
        $group->setRegisterInArrayValidator(false);
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Adicionar Usuário");
        
        $this->addElements(array($name, $mail, $username, $password, $group, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            ));
    }

}


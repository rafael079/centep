<?php

class Administrador_Form_Login extends Zend_Form
{

    public function init()
    {
        $this->setAction("")
             ->setMethod('post');
        
        $username = new Zend_Form_Element_Text('username');
        $username->setRequired(true)
             ->setAttribs(array('class' => 'required noerror', 'tabindex' => 1))
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(true)
             ->setAttribs(array('class' => 'required noerror', 'tabindex' => 2))
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Acessar")
               ->setAttribs(array('tabindex' => 2));
        
        $this->addElements(array($username, $password, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            ));
    }


}


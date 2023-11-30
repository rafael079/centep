<?php

class Alunos_Form_Dados extends Zend_Form {

    public function init() {
        $this->setAction('')->setMethod('post');
        
        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
                ->setAttribs(array("class" => "contact"))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $mail = new Zend_Form_Element_Text('mail');
        $mail->setRequired(true)
                ->setAttribs(array("class" => "contact"))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(FALSE)
                ->setAttribs(array("class" => "contact"))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
       
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Editar Dados")
                ->setAttribs(array('class' => 'btn btn-small but_top'));

        $this->addElements(array($name, $mail, $password, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }

}


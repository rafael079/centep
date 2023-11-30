<?php

class Administrador_Form_Teacher2 extends Zend_Form {

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
                ->setAttrib("email", "true")
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

        $disciplines = new Zend_Form_Element_Multiselect('disciplines');
        $disciplines->setRequired(true)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required dualselects")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $disciplines->setRegisterInArrayValidator(false);

        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Adicionar Professor");

        $this->addElements(array($name, $mail, $disciplines, $username, $password, $submit));

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }

}

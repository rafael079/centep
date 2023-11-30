<?php

class Administrador_Form_Configuracoes extends Zend_Form {

    public function init() {

        $this->setAction("")
             ->setMethod('post');
        
        $title = new Zend_Form_Element_Text('title');
        $title->setRequired(true)
              ->setAttrib("data-error-type", "inline")
              ->setAttrib("class", "required")
              ->addFilter('StripTags')
              ->addFilter('StringTrim');
        
        $slogan = new Zend_Form_Element_Text('slogan');
        $slogan->setRequired(true)
               ->setAttrib("data-error-type", "inline")
               ->setAttrib("class", "required")
               ->addFilter('StripTags')
               ->addFilter('StringTrim');
        
        $email = new Zend_Form_Element_Text('email');
        $email->setRequired(true)
              ->setAttrib("data-error-type", "inline")
              ->setAttrib("class", "email")
              ->addFilter('StripTags')
              ->addFilter('StringTrim');
        
        $tags = new Zend_Form_Element_Textarea('tags');
        $tags->setRequired(true)
             ->setAttrib("data-error-type", "inline")
             ->setAttrib("class", "required")
             ->setAttrib('rows', '5')
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->setRequired(true)
                    ->setAttrib("data-error-type", "inline")
                    ->setAttrib("class", "required")
                    ->setAttrib('rows', '5')
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Atualizar Dados");
        
        $this->addElements(array($title, $slogan, $email, $tags, $description, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            ));
        
    }

}


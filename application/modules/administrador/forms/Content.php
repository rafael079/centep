<?php

class Administrador_Form_Content extends Zend_Form
{

    public function init()
    {
        $this->setAction("")
             ->setMethod('post');
        
        $title = new Zend_Form_Element_Text('title');
        $title->setRequired(true)
             ->setAttrib("data-error-type", "inline")
             ->setAttrib("class", "required")
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $text = new Zend_Form_Element_Textarea('text');
        $text->setRequired(true)
             ->setAttribs(array('class' => 'required editor', 'data-error-type' => 'inline'))
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Adicionar Conteudo");
        
        $this->addElements(array($title, $text, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            ));
    }


}


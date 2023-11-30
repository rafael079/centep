<?php

class Professores_Form_Time extends Zend_Form {

    public function init() {
        $this->setAction('')->setMethod('post');
        
        $entry = new Zend_Form_Element_Text('entry');
        $entry->addFilter('StripTags')
              ->addFilter('StringTrim');
        
        $exit = new Zend_Form_Element_Text('exit');
        $exit->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Marcar Ponto")
                ->setAttribs(array('class' => 'btn btn-small but_top'));

        $this->addElements(array($entry, $exit, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors'
        ));
        
    }

}


<?php

class Professores_Form_Files extends Zend_Form {

    public function init() {
        $this->setAction('')->setMethod('post')->setAttrib("enctype", "multipart/form-data");;
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->setRequired(true)
                ->setAttribs(array("class" => "contact"))
                ->setAttribs(array('cols' => 40, 'rows' => 5, 'class' => 'contact_width'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Enviar Pergunta")
                ->setAttribs(array('class' => 'btn btn-small but_top'));

        $this->addElements(array($description, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors'
        ));
    }

}


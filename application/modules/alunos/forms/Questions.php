<?php

class Alunos_Form_Questions extends Zend_Form
{

    public function init()
    {
        $this->setAction('')->setMethod('post');
        
        $teacher = new Zend_Form_Element_Select('teacher');
        $teacher->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $teacher->setRegisterInArrayValidator(false);
        
        $message = new Zend_Form_Element_Textarea('message');
        $message->setRequired(true)
                ->setAttribs(array("class" => "contact"))
                ->setAttribs(array('cols' => 40, 'rows' => 10, 'class' => 'contact_width'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Enviar Pergunta")
                ->setAttribs(array('class' => 'btn btn-small but_top'));

        $this->addElements(array($teacher, $message, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }


}


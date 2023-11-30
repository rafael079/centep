<?php

class Administrador_Form_Mails extends Zend_Form
{

    public function init()
    {
        $this->setAction("")
             ->setMethod('post');
        
        $subject = new Zend_Form_Element_Text('subject');
        $subject->setRequired(true)
             ->setAttribs(array("data-error-type" => "inline", "class" => "required" ))
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $students = new Zend_Form_Element_Select('students');
        $students->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("data-placeholder", "Selecione por alunos")
                ->setAttrib("class", "required search")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $students->setRegisterInArrayValidator(false);
        
        $courses = new Zend_Form_Element_Select('courses');
        $courses->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("data-placeholder", "Selecione por cursos")
                ->setAttrib("class", "required search")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $courses->setRegisterInArrayValidator(false);
        
        $senders = new Zend_Form_Element_Textarea('senders');
        $senders->setRequired(false)
                    ->setAttribs(array("rows" => 4))
                    //->addFilter('StripTags')
                    ->addFilter('StringTrim');
        
        $text = new Zend_Form_Element_Textarea('text');
        $text->setRequired(true)
                    ->setAttribs(array("class" => "required editor", "data-error-type" => "inline"))
                    //->addFilter('StripTags')
                    ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Enviar Mensagem");
        
        $this->addElements(array($subject, $students, $courses, $senders, $text, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            ));
        
    }


}


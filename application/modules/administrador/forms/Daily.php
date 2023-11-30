<?php

class Administrador_Form_Daily extends Zend_Form
{

    public function init()
    {
        $this->setMethod("post");
        
        $classe = new Zend_Form_Element_Select('classe');
        $classe->setRequired(false)
               ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
               ->addFilter('StripTags')
               ->addFilter('StringTrim');
        
        $module = new Zend_Form_Element_Select('module');
        $module->setRequired(false)
                ->addMultiOptions(array('1' => 'Módulo 1', '2' => 'Módulo 2', '3' => 'Módulo 3', '4' => 'Módulo 4',))
               ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
               ->addFilter('StripTags')
               ->addFilter('StringTrim');
        
        $teacher = new Zend_Form_Element_Select('teacher');
        $teacher->setRequired(false)
               ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
               ->addFilter('StripTags')
               ->addFilter('StringTrim');
        
        $course = new Zend_Form_Element_Select('course');
        $course->setRequired(false)
               ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
               ->addFilter('StripTags')
               ->addFilter('StringTrim');
        
        $discipline = new Zend_Form_Element_Select('discipline');
        $discipline->setRequired(false)
               ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
               ->addFilter('StripTags')
               ->addFilter('StringTrim');
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Gerar Diário");

        $this->addElements(array($classe, $teacher, $course, $module, $discipline, $submit));

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }


}


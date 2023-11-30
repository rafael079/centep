<?php

class Contato_Form_Contact extends Zend_Form {

    public function init() {
        $this->setAction('')->setMethod('post');

        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
                ->setAttribs(array('size' => 40, 'class' => 'contact'))
                ->addFilter('StripTags')
                ->setErrorMessages(array("isEmpty" => "Por favor, informe seu nome"))
                ->addFilter('StringTrim');
        
        $mail = new Zend_Form_Element_Text('mail');
        $mail->setRequired(true)
                ->setAttribs(array('size' => 40, 'class' => 'contact'))
                ->addFilter('StripTags')
                ->setErrorMessages(array("isEmpty" => "Por favor, informe seu e-mail"))
                ->addValidator('EmailAddress',  TRUE)
                ->addFilter('StringTrim');
        
        $phone = new Zend_Form_Element_Text('phone');
        $phone->setRequired(false)
                ->setAttribs(array('size' => 40, 'class' => 'contact'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $subject = new Zend_Form_Element_Text('subject');
        $subject->setRequired(true)
                ->setErrorMessages(array("isEmpty" => "Por favor, informe um assunto para a mensagem"))
                ->setAttribs(array('size' => 40, 'class' => 'contact'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        
        $message = new Zend_Form_Element_Textarea('message');
        $message->setRequired(true)
                ->setErrorMessages(array("isEmpty" => "Por favor, escreva a sua mensagem"))
                ->setAttribs(array('cols' => 40, 'rows' => 10, 'class' => 'contact_width'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Enviar Mensagem")
                ->setAttribs(array('class' => 'btn btn-small but_top'));

        $this->addElements(array($name, $mail, $phone, $subject, $message, $submit));

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }

}


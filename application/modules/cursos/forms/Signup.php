<?php

class Cursos_Form_Signup extends Zend_Form {

    public function init() {
        $this->setMethod("post")->setAction('');

        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
                ->setAttribs(array("class" => "contact"))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $mail = new Zend_Form_Element_Text('mail');
        $mail->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $identity = new Zend_Form_Element_Text('identity');
        $identity->setRequired(true)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $cpf = new Zend_Form_Element_Text('cpf');
        $cpf->setRequired(true)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required maskCpf")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $birth = new Zend_Form_Element_Text('birth');
        $birth->setRequired(true)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required maskDate")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $racial = new Zend_Form_Element_Text('racial');
        $racial->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("placeholder", "Ex: Mulato ou Negro")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $sex = new Zend_Form_Element_Select('sex');
        $sex->setRequired(false)
                ->addMultiOptions(array('', 'm' => 'Masculino', 'f' => 'Feminino'))
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $nationality = new Zend_Form_Element_Text('nationality');
        $nationality->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->setAttrib("placeholder", "Ex: Brasil")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $naturalness = new Zend_Form_Element_Text('naturalness');
        $naturalness->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->setAttrib("placeholder", "Ex: Belo Horizonte")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $state = new Zend_Form_Element_Text('state');
        $state->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->setAttrib("placeholder", "Ex: Minas Gerais")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $phone = new Zend_Form_Element_Text('phone');
        $phone->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required maskPhone")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $needs = new Zend_Form_Element_Checkbox('needs');
        $needs->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $needs_details = new Zend_Form_Element_Text('needs_details');
        $needs_details->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $father = new Zend_Form_Element_Text('father');
        $father->setRequired(false)
                ->setAttribs(array("class" => "contact"))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $job_father = new Zend_Form_Element_Text('job_father');
        $job_father->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $mother = new Zend_Form_Element_Text('mother');
        $mother->setRequired(false)
                ->setAttribs(array("class" => "contact"))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $job_mother = new Zend_Form_Element_Text('job_mother');
        $job_mother->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $responsible = new Zend_Form_Element_Text('responsible');
        $responsible->setRequired(false)
                ->setAttribs(array("class" => "contact"))
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $responsible_parent = new Zend_Form_Element_Text('responsible_parent');
        $responsible_parent->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $address = new Zend_Form_Element_Text('address');
        $address->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $number = new Zend_Form_Element_Text('number');
        $number->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $complement = new Zend_Form_Element_Text('complement');
        $complement->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $neighborhood = new Zend_Form_Element_Text('neighborhood');
        $neighborhood->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $city = new Zend_Form_Element_Text('city');
        $city->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $state_address = new Zend_Form_Element_Text('state_address');
        $state_address->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $cep = new Zend_Form_Element_Text('cep');
        $cep->setRequired(false)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $username = new Zend_Form_Element_Text('username');
        $username->setRequired(TRUE)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(TRUE)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("class", "required")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $courses = new Zend_Form_Element_Select('courses');
        $courses->setRequired(true)
                ->setAttrib("data-error-type", "inline")
                ->setAttrib("data-placeholder", "Escolha um Curso")
                ->setAttrib("class", "required search")
                ->addFilter('StripTags')
                ->addFilter('StringTrim');
        $courses->setRegisterInArrayValidator(false);

        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Enviar o Cadastro")
                ->setAttribs(array('class' => 'btn btn-small but_top'));

        $this->addElements(array(
            $name,
            $mail,
            $identity,
            $cpf,
            $birth,
            $racial,
            $sex,
            $nationality,
            $naturalness,
            $state,
            $phone,
            $needs,
            $needs_details,
            $father,
            $job_father,
            $mother,
            $job_mother,
            $responsible,
            $responsible_parent,
            $address,
            $number,
            $complement,
            $neighborhood,
            $city,
            $state_address,
            $cep,
            $username,
            $password,
            $courses,
            $submit));

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }

}


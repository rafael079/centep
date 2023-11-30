<?php

class Administrador_Form_Groups extends Zend_Form {

    public function init() {
        $this->setAction("")
             ->setMethod('post');
        
        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
             ->setAttrib("data-error-type", "inline")
             ->setAttrib("class", "required")
             ->addFilter('StripTags')
             ->addFilter('StringTrim');
        
        $permissions = new Zend_Form_Element_Multiselect('permissions');
        $permissions->setRequired(true)
                    ->addMultiOptions(array( '1' => 'Todas as Opções', 
                                             '2' => 'Gerenciar Usuarios', 
                                             '3' => 'Gerenciar Professores', 
                                             '10' => 'Gerenciar Alunos', 
                                             '8' => 'Gerenciar Cursos', 
                                             '9' => 'Gerenciar Disciplinas', 
                                             '4' => 'Lançar Notas', 
                                             '5' => 'Configurar o Sistema',
                                             '11' => 'Gerenciar Turmas',
                                             '6' => 'Usar Ferramentas',
                                             '7' => 'Emitir Relatórios'));
        
        $submit = new Zend_Form_Element_Submit('send');
        $submit->setLabel("Adicionar Grupo");
        
        $this->addElements(array($name, $permissions, $submit));
        
        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            ));
    }

}


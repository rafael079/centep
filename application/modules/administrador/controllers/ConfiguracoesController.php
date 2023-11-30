<?php

class Administrador_ConfiguracoesController extends Zend_Controller_Action {

    public function init() {
        $this->auth = Zend_Auth::getInstance();
        $this->auth->setStorage(new Zend_Auth_Storage_Session('User_Administrador'));
        if (!$this->auth->hasIdentity()) {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Efetue o login para acessar esta área!'));
            $this->_helper->redirector->gotoSimple('index', 'login', 'administrador');
        }
        $this->_helper->layout->setLayout('administrador');
    }

    public function indexAction() {

        $user = Zend_Auth::getInstance()->getIdentity();

        $teachers = new Administrador_Model_Users();
        $results = $teachers->fetchRow("id='" . $user->id . "'");
        $this->view->user = $results;

        $groups = new Administrador_Model_Groups();
        $permissions = $groups->fetchRow($results->group);
        $getPermissions = explode('|', $permissions->permissions);

        if (!(in_array(1, $getPermissions))) {
            if (!(in_array(5, $getPermissions))) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Você não tem permissão para acessar esta área!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'administrador');
            }
        }

        $configs = new Zend_Config_Xml(APPLICATION_PATH . '/configs/settings.xml', 'web');

        $form = new Administrador_Form_Configuracoes();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $config = new Zend_Config(array(), true);
                $config->web = array();
                $config->web->title = $form->title->getValue();
                $config->web->slogan = $form->slogan->getValue();
                $config->web->email = $form->email->getValue();
                $config->web->tags = $form->tags->getValue();
                $config->web->description = $form->description->getValue();
                $writer = new Zend_Config_Writer_Xml();
                $writer->write(APPLICATION_PATH . '/configs/settings.xml', $config);
                $this->_helper->flashMessenger->addMessage(array('success' => 'Dados enviados com sucesso!'));
                return $this->_helper->redirector->gotoSimple('index', 'configuracoes', 'administrador');
            }
        }

        $form->title->setValue($configs->title);
        $form->slogan->setValue($configs->slogan);
        $form->email->setValue($configs->email);
        $form->tags->setValue($configs->tags);
        $form->description->setValue($configs->description);

        $this->view->form = $form;
    }

}


<?php

class Administrador_IndexController extends Zend_Controller_Action {

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
    }

}


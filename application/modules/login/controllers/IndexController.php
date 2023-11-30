<?php

class Login_IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $form = new Login_Form_Login();
        $this->view->form = $form;
    }

    public function alunosAction() {
        if ($this->getRequest()->isPost()) {

            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
            $authAdapter->setTableName('students')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password')
                    ->setCredentialTreatment('SHA1(?)');

            $authAdapter->setIdentity($_POST['username'])
                    ->setCredential($_POST['passowrd']);

            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);



            if ($result->isValid()) {
                $info = $authAdapter->getResultRowObject(null, 'password');
                $auth->setStorage(new Zend_Auth_Storage_Session('User_Aluno'));
                $storage = $auth->getStorage();
                $storage->write($info);
                return $this->_helper->redirector->gotoSimple('index', 'index', 'alunos');
            } else {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Usuário ou senha estão incorretos!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'login');
            }
        }
    }

    public function professoresAction() {
        if ($this->getRequest()->isPost()) {

            $dbAdapter = Zend_Db_Table::getDefaultAdapter();
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
            $authAdapter->setTableName('teachers')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password')
                    ->setCredentialTreatment('SHA1(?)');

            $authAdapter->setIdentity($_POST['username'])
                    ->setCredential($_POST['passowrd']);

            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);

            if ($result->isValid()) {
                $info = $authAdapter->getResultRowObject(null, 'password');
                $auth->setStorage(new Zend_Auth_Storage_Session('User_Professor'));
                $storage = $auth->getStorage();
                $storage->write($info);
                return $this->_helper->redirector->gotoSimple('index', 'index', 'professores');
            } else {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Usuário ou senha estão incorretos!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'login');
            }
        }
    }

}


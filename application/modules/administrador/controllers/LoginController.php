<?php

class Administrador_LoginController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('login');
    }

    public function indexAction()
    {
        $form = new Administrador_Form_Login();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {

                $dbAdapter = Zend_Db_Table::getDefaultAdapter();
                $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
                $authAdapter->setTableName('users')
                        ->setIdentityColumn('username')
                        ->setCredentialColumn('password')
                        ->setCredentialTreatment('SHA1(?)');

                $authAdapter->setIdentity($form->username->getValue())
                        ->setCredential($form->password->getValue());

                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                if ($result->isValid()) {
                    $info = $authAdapter->getResultRowObject(null, 'password');
                    $auth->setStorage(new Zend_Auth_Storage_Session('User_Administrador'));
                    $storage = $auth->getStorage();
                    $storage->write($info);
                    return $this->_helper->redirector->gotoSimple('index', 'index', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Usuário ou senha estão incorretos!'));
                    return $this->_helper->redirector->gotoSimple('index', 'login', 'administrador');
                }
            }
        }

        $this->view->form = $form;
    }

    public function sairAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('User_Administrador'));
        $auth->clearIdentity();
        return $this->_helper->redirector->gotoSimple('index', 'login', 'administrador');
    }


}




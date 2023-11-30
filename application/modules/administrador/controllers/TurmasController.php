<?php

class Administrador_TurmasController extends Zend_Controller_Action
{

    public function init()
    {
        $this->auth = Zend_Auth::getInstance();
        $this->auth->setStorage(new Zend_Auth_Storage_Session('User_Administrador'));
        if (!$this->auth->hasIdentity()) {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Efetue o login para acessar esta área!'));
            $this->_helper->redirector->gotoSimple('index', 'login', 'administrador');
        }
                        
        $user = Zend_Auth::getInstance()->getIdentity();

        $teachers = new Administrador_Model_Users();
        $results = $teachers->fetchRow("id='" . $user->id . "'");
        $this->view->user = $results;

        $groups = new Administrador_Model_Groups();
        $permissions = $groups->fetchRow($results->group);
        $getPermissions = explode('|', $permissions->permissions);

        if (!(in_array(1, $getPermissions))) {
            if (!(in_array(11, $getPermissions))) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Você não tem permissão para acessar esta área!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'administrador');
            }
        }
        
        $this->_helper->layout->setLayout('administrador');
    }

    public function indexAction()
    {
        $model = new Administrador_Model_Classes();
        $results = $model->fetchAll();
        $this->view->results = $results;
    }

    public function novaAction()
    {
        $form = new Administrador_Form_Classes();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $model = new Administrador_Model_Classes();
                $insert = $model->insert(array('name' => $form->name->getValue(), 'date' => date("Y-m-d H:i:s")));
                if($insert) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Turma inserida com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'turmas', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao inserir a turma'));
                    return $this->_helper->redirector->gotoSimple('index', 'turmas', 'administrador');
                }
            }
        }
        $this->view->form = $form;
    }

    public function editarAction()
    {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {
            $model = new Administrador_Model_Classes();
            $results = $model->fetchRow("id='" . $parametros['id'] . "'");
        $form = new Administrador_Form_Classes();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                
                $update = $model->update(array('name' => $form->name->getValue(), 'date' => date("Y-m-d H:i:s")), "id='" . $parametros['id'] . "'");
                if($update) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Turma editada com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'turmas', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao editar a turma'));
                    return $this->_helper->redirector->gotoSimple('index', 'turmas', 'administrador');
                }
            }
        }
        $form->name->setValue($results->name);
        $this->view->form = $form;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Informe uma turma para editar!'));
                    return $this->_helper->redirector->gotoSimple('index', 'turmas', 'administrador');
        }
    }


}






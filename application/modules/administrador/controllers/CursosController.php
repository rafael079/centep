<?php

class Administrador_CursosController extends Zend_Controller_Action {

    public function init() {
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
            if (!(in_array(8, $getPermissions))) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Você não tem permissão para acessar esta área!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'administrador');
            }
        }
        
        $this->_helper->layout->setLayout('administrador');
    }

    public function indexAction() {
        $model = new Administrador_Model_Courses();
        $results = $model->fetchAll($model->select()->from("courses", array('id', 'name', 'value', 'workload', 'status')));
        $this->view->results = $results;
    }

    public function adicionarAction() {
        $form = new Administrador_Form_Courses();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $model = new Administrador_Model_Courses();
                $values = array(
                    'name' => $form->name->getValue(),
                    'description' => $form->description->getValue(),
                    'value' => $form->value->getValue(),
                    'workload' => $form->workload->getValue(),
                    'status' => 1,
                    'date' => date("Y-m-d H:i:s"),
                );
                $inset = $model->insert($values);
                if ($inset) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Curso adicionado com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'cursos', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Ocorreu um erro ao adicionar o curso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'cursos', 'administrador');
                }
            }
        }

        $this->view->form = $form;
    }

    public function editarAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {

            $form = new Administrador_Form_Courses();

            $model = new Administrador_Model_Courses();

            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {

                    $values = array(
                        'name' => $form->name->getValue(),
                        'description' => $form->description->getValue(),
                        'value' => $form->value->getValue(),
                        'workload' => $form->workload->getValue(),
                        'status' => 1,
                        'date' => date("Y-m-d H:i:s"),
                    );
                    $update = $model->update($values, "id='" . $parametros['id'] . "'");
                    if ($update) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Curso editado com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('index', 'cursos', 'administrador');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Ocorreu um erro ao editar o curso!'));
                        return $this->_helper->redirector->gotoSimple('index', 'cursos', 'administrador');
                    }
                }
            }

            $results = $model->fetchRow($model->select()->from("courses", array('id', 'name', 'value', 'description', 'workload', 'status'))->where("id='" . $parametros['id'] . "'"));

            $form->name->setValue($results->name);
            $form->description->setValue($results->description);
            $form->value->setValue($results->value);
            $form->workload->setValue($results->workload);
            $form->send->setLabel("Editar Curso");

            $this->view->form = $form;
            
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um curso para editar!'));
            return $this->_helper->redirector->gotoSimple('index', 'cursos', 'administrador');
        }
    }

    public function excluirAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {
            $model = new Administrador_Model_Courses();
            $results = $model->fetchRow($model->select()->from("courses", array('id', 'name'))->where("id='" . $parametros['id'] . "'"));
            $this->view->results = $results;
            if (isset($parametros['confirmar']) && $parametros['confirmar'] == 1) {
                $delete = $model->delete("id='" . $parametros['id'] . "'");
                if ($delete) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Curso excluido com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'cursos', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Ocorreu um erro ao excluir o curso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'cursos', 'administrador');
                }
            }
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um curso para excluir!'));
            return $this->_helper->redirector->gotoSimple('index', 'cursos', 'administrador');
        }
    }

}


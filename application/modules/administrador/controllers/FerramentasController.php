<?php

class Administrador_FerramentasController extends Zend_Controller_Action {

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
            if (!(in_array(6, $getPermissions))) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Você não tem permissão para acessar esta área!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'administrador');
            }
        }

        $this->_helper->layout->setLayout('administrador');
    }

    public function indexAction() {
        // action body
    }

    public function boletosAction() {
        
    }

    public function emailsAction() {
        $form = new Administrador_Form_Mails();

        $students = new Administrador_Model_Students();
        $studentsResults = $students->fetchAll($students->select()
                        ->from('students', array('id', 'name')));

        $student = array();
        $student[''] = '';
        foreach ($studentsResults as $value) {
            $student[$value['id']] = $value['name'];
        }

        $courses = new Administrador_Model_Courses();
        $coursesResults = $courses->fetchAll($courses->select()
                        ->from('courses', array('id', 'name')));

        $course = array();
        $course[''] = '';
        foreach ($coursesResults as $valueCourse) {
            $course[$valueCourse['id']] = $valueCourse['name'];
        }

        $form->students->addMultiOptions($student);
        $form->courses->addMultiOptions($course);
        $this->view->form = $form;
    }

    public function conteudoAction() {
        $model = new Administrador_Model_Content();
        $results = $model->fetchAll();
        $this->view->results = $results;
    }

    public function adicionarConteudoAction() {
        $form = new Administrador_Form_Content();
        $model = new Administrador_Model_Content();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $values = array(
                    'title' => $form->title->getValue(),
                    'text' => $form->text->getValue(),
                    'date' => date("Y-m-d H:i:s"),
                );
                $insert = $model->insert($values);
                if ($insert) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Dados enviados com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('conteudo', 'ferramentas', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao enviar o conteudo!'));
                    return $this->_helper->redirector->gotoSimple('conteudo', 'ferramentas', 'administrador');
                }
            }
        }

        $this->view->form = $form;
    }

    public function editarConteudoAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {
            $form = new Administrador_Form_Content();

            $model = new Administrador_Model_Content();
            $results = $model->fetchRow("id='" . $parametros['id'] . "'");

            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {
                    $values = array(
                        'title' => $form->title->getValue(),
                        'text' => $form->text->getValue(),
                        'date' => date("Y-m-d H:i:s"),
                    );
                    $update = $model->update($values, "id='" . $parametros['id'] . "'");
                    if ($update) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Dados editado com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('conteudo', 'ferramentas', 'administrador');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao editar o conteudo!'));
                        return $this->_helper->redirector->gotoSimple('conteudo', 'ferramentas', 'administrador');
                    }
                }
            }
            $form->title->setValue($results->title);
            $form->text->setValue($results->text);
            $this->view->form = $form;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Informe um conteudo para editar!'));
            return $this->_helper->redirector->gotoSimple('conteudo', 'ferramentas', 'administrador');
        }
    }

}


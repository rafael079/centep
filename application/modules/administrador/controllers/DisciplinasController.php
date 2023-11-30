<?php

class Administrador_DisciplinasController extends Zend_Controller_Action {

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
            if (!(in_array(9, $getPermissions))) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Você não tem permissão para acessar esta área!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'administrador');
            }
        }
        
        $this->_helper->layout->setLayout('administrador');
    }

    public function indexAction() {
        
        $model = new Administrador_Model_Disciplines();
        
        $select = $model->select()
                         ->setIntegrityCheck(false)
                         ->from(array('A' => 'disciplines'), 
                                array('id', 'name', 'workload'))
                         ->joinLeft(array("B" => 'courses'), "A.course = B.id", array('courses' => 'name'));
        
        $results = $model->fetchAll($select);
        $this->view->results = $results;

    }

    public function adicionarAction() {
        
        $form = new Administrador_Form_Disciplines();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $model = new Administrador_Model_Disciplines();
                $values = array(
                    'name' => $form->name->getValue(),
                    'description' => $form->description->getValue(),
                    'course' => $form->course->getValue(),
                    'workload' => $form->workload->getValue(),
                    'date' => date("Y-m-d H:i:s"),
                );
                $inset = $model->insert($values);
                if ($inset) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Disciplina adicionada com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'disciplinas', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Ocorreu um erro ao adicionar a disciplina!'));
                    return $this->_helper->redirector->gotoSimple('index', 'disciplinas', 'administrador');
                }
            }
        }

        $course = new Administrador_Model_Courses();
        $results = $course->fetchAll($course->select()->from('courses', array('id', 'name')));
        foreach ($results as $value) {
            $array[$value['id']] = $value['name'];
        }
        $form->course->addMultiOptions($array);
        
        $this->view->form = $form;
    }

    public function editarAction() {

        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {

            $form = new Administrador_Form_Disciplines();

            $model = new Administrador_Model_Disciplines();

            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {

                    $values = array(
                        'name' => $form->name->getValue(),
                        'description' => $form->description->getValue(),
                        'course' => $form->course->getValue(),
                        'workload' => $form->workload->getValue(),
                        'date' => date("Y-m-d H:i:s"),
                    );

                    $update = $model->update($values, "id='" . $parametros['id'] . "'");
                    if ($update) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Disciplina adicionada com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('index', 'disciplinas', 'administrador');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Ocorreu um erro ao adicionar a disciplina!'));
                        return $this->_helper->redirector->gotoSimple('index', 'disciplinas', 'administrador');
                    }
                }
            }

            $course = new Administrador_Model_Courses();
            $results = $course->fetchAll($course->select()->from('courses', array('id', 'name')));
            foreach ($results as $value) {
                $array[$value['id']] = $value['name'];
            }

            $disciplines = $model->fetchRow($model->select()->from('disciplines', array('id', 'name', 'description', 'workload', 'course'))->where("id='" . $parametros['id'] . "'"));

            $form->name->setValue($disciplines->name);
            $form->description->setValue($disciplines->description);
            $form->workload->setValue($disciplines->workload);
            $form->course->addMultiOptions($array)->setValue($disciplines->course);
            $this->view->form = $form;
            
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione uma disciplina para editar!'));
            return $this->_helper->redirector->gotoSimple('index', 'disciplinas', 'administrador');
        }
    }

    public function excluirAction() {
        
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {
            
            $model = new Administrador_Model_Disciplines();
            $results = $model->fetchRow($model->select()->from('disciplines', array('id', 'name'))->where("id='" . $parametros['id'] . "'"));
            
            if(isset($parametros['confirmar']) && ($parametros['confirmar'] == 1)) {
                $delete = $model->delete("id='" . $parametros['id'] . "'");
                if ($delete) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Disciplina excluida com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'disciplinas', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Ocorreu um erro ao excluir a disciplina!'));
                    return $this->_helper->redirector->gotoSimple('index', 'disciplinas', 'administrador');
                }
            }
            
            $this->view->results = $results;
            
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione uma disciplina para excluir!'));
            return $this->_helper->redirector->gotoSimple('index', 'disciplinas', 'administrador');
        }
    }

}


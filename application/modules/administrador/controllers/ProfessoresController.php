<?php

class Administrador_ProfessoresController extends Zend_Controller_Action {

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
            if (!(in_array(3, $getPermissions))) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Você não tem permissão para acessar esta área!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'administrador');
            }
        }

        $this->_helper->layout->setLayout('administrador');
    }

    public function indexAction() {
        $model = new Administrador_Model_Teachers();
        $select = $model->select()
                ->setIntegrityCheck(false)
                ->from(array('A' => 'teachers'), array('id', 'name', 'username'))
                ->joinLeft(array("B" => 'courses'), "A.courses = B.id", array('courses' => 'name'));

        $results = $model->fetchAll($select);

        $this->view->results = $results;
    }

    public function adicionarAction() {

        $form = new Administrador_Form_Teachers();

        $model = new Administrador_Model_Courses();
        $courses = $model->fetchAll($model->select()->from('courses', array('id', 'name')));

        $course = array();
        foreach ($courses as $value) {
            $course[$value['id']] = $value['name'];
        }
        $form->courses->addMultiOptions($course);

        $form->setAction($this->view->url(array('module' => 'administrador', 'controller' => 'professores', 'action' => 'completar-cadastro')));
        $this->view->form = $form;
    }

    public function completarCadastroAction() {

        if (isset($_POST['courses']) && !empty($_POST['courses'])) {

            $form = new Administrador_Form_Teacher2();

            $courses = new Administrador_Model_Courses();
            $course = $courses->fetchRow($courses->select()->from('courses', array('name'))->where("id='" . $_POST['courses'] . "'"));

            $this->view->course = $course['name'];

            $disciplines = new Administrador_Model_Disciplines();
            $getDisciplines = $disciplines->fetchAll($disciplines->select()->from('disciplines', array('id', 'name'))->where("course='" . $_POST['courses'] . "'"));

            $discipline = array();
            foreach ($getDisciplines as $value) {
                $discipline[$value['id']] = $value['name'];
            }

            $form->disciplines->addMultiOptions($discipline);

            $form->setAction($this->view->url(array('module' => 'administrador', 'controller' => 'professores', 'action' => 'finalizar-cadastro', 'courses' => $_POST['courses'])));
            $this->view->form = $form;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Você tem que informar um curso para cadastrar o Professor!'));
            return $this->_helper->redirector->gotoSimple('adicionar', 'professores', 'administrador');
        }
    }

    public function finalizarCadastroAction() {

        $parametros = $this->getAllParams();

        if (isset($parametros['courses'])) {

            $form = new Administrador_Form_Teacher2();

            if ($this->getRequest()->isPost()) {

                $array = implode("|", $_POST['disciplines']);

                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {

                    $model = new Administrador_Model_Teachers();

                    $values = array(
                        'name' => $form->name->getValue(),
                        'username' => $form->username->getValue(),
                        'password' => sha1($form->password->getValue()),
                        'mail' => $form->mail->getValue(),
                        'courses' => $parametros['courses'],
                        'disciplines' => $array,
                        'date' => date("Y-m-d H:i:s"),
                    );
                    $inset = $model->insert($values);
                    if ($inset) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Professor adicionado com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('index', 'professores', 'administrador');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao tentar adicionar o Professor!'));
                        return $this->_helper->redirector->gotoSimple('index', 'professores', 'administrador');
                    }
                }
            }
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Você tem que informar um curso para cadastrar o Professor!'));
            return $this->_helper->redirector->gotoSimple('adicionar', 'professores', 'administrador');
        }
    }

    public function editarAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {

            $form = new Administrador_Form_Teacher2();
            $model = new Administrador_Model_Teachers();

            $results = $model->fetchRow($model->select()
                            ->setIntegrityCheck(false)
                            ->from(array('A' => 'teachers'), array('id', 'name', 'mail', 'username', 'password', 'disciplines'))
                            ->joinLeft(array('B' => 'courses'), "A.courses = B.id", array('course' => 'name', 'course_id' => 'id'))
                            ->where("A.id='" . $parametros['id'] . "'"));

            if ($this->getRequest()->isPost()) {

                $_POST['username'] = $results->username;

                if (empty($_POST['password'])) {
                    $_POST['password'] = $results->password;
                } else {
                    $_POST['password'] = sha1($_POST['password']);
                }

                $array = implode("|", $_POST['disciplines']);

                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {

                    $model = new Administrador_Model_Teachers();

                    $values = array(
                        'name' => $form->name->getValue(),
                        'username' => $form->username->getValue(),
                        'password' => $form->password->getValue(),
                        'mail' => $form->mail->getValue(),
                        'courses' => $results->course_id,
                        'disciplines' => $array,
                        'date' => date("Y-m-d H:i:s"),
                    );
                    $inset = $model->update($values, "id='" . $parametros['id'] . "'");
                    if ($inset) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Professor editado com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('index', 'professores', 'administrador');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao tentar editar o Professor!'));
                        return $this->_helper->redirector->gotoSimple('index', 'professores', 'administrador');
                    }
                }
            }

            $form->name->setValue($results->name);
            $form->mail->setValue($results->mail);

            $disciplines = new Administrador_Model_Disciplines();
            $explode = explode("|", $results->disciplines);
            $select = $disciplines->select()->from('disciplines', array('id', 'name'));

            foreach ($explode as $value) {
                $select->orWhere("id='" . $value . "'");
            }

            $discipline = $disciplines->fetchAll($select)->toArray();

            $array = array();
            foreach ($discipline as $value) {
                $array[$value['id']] = $value['name'];
            }

            $allresults = $disciplines->fetchAll($disciplines->select()->from('disciplines', array('id', 'name'))->where("course='" . $results->course_id . "'"))->toArray();
            $array2 = array();
            foreach ($allresults as $value) {
                $array2[$value['id']] = $value['name'];
            }

            $form->disciplines->setValue($explode)->addMultiOptions($array2);
            $form->username->setValue($results->username)->setIgnore(true)->setAttribs(array('disabled' => 'disabled'));
            $form->password->setAttribs(array('class' => ''));

            $this->view->course = $results->course;
            $this->view->form = $form;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um professor para editar!'));
            return $this->_helper->redirector->gotoSimple('index', 'professores', 'administrador');
        }
    }

    public function excluirAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {

            $model = new Administrador_Model_Teachers();
            $results = $model->fetchRow($model->select()->from("teachers", array("id", "name"))->where("id='" . $parametros['id'] . "'"));
            $this->view->results = $results;

            if (isset($parametros['confirmar']) && ($parametros['confirmar'] == 1)) {
                $delete = $model->delete("id='" . $parametros['id'] . "'");
                if ($delete) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Professor excluido com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'professores', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao exluir o Professor!'));
                    return $this->_helper->redirector->gotoSimple('index', 'professores', 'administrador');
                }
            }
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um Professor para excluir!'));
            return $this->_helper->redirector->gotoSimple('index', 'professores', 'administrador');
        }
    }

    public function pontoAction() {

        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {

            $model = new Administrador_Model_Teachers();
            $select = $model->select()
                    ->setIntegrityCheck(false)
                    ->from(array("A" => 'teachers_time'), array('teachers_date' => 'date', 'teachers_entry' => 'entry', 'teachers_exit' => 'exit' ))
                    ->joinLeft(array('C' => 'teachers'), "C.id = A.teachers",  array('id', 'name'))
                    ->joinLeft(array("B" => 'courses'), "C.courses = B.id", array('courses' => 'name'))
                    ->where("A.teachers='" . $parametros['id'] . "'")
                    ->order("A.date DESC");

            $results = $model->fetchAll($select);

            $this->view->results = $results;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um Professor!'));
            return $this->_helper->redirector->gotoSimple('index', 'professores', 'administrador');
        }
    }

}


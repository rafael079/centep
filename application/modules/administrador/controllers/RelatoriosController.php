<?php

class Administrador_RelatoriosController extends Zend_Controller_Action {

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
            if (!(in_array(7, $getPermissions))) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Você não tem permissão para acessar esta área!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'administrador');
            }
        }

        $this->_helper->layout->setLayout('administrador');
    }

    public function indexAction() {
        // action body
    }

    public function diarioAction() {
        $form = new Administrador_Form_Daily();

        $classes = new Administrador_Model_Classes();
        $getClasses = $classes->fetchAll($classes->select()->from('classes', array('id', 'name')));
        $classe = array();
        foreach ($getClasses as $valueClasse) {
            $classe[$valueClasse['id']] = $valueClasse['name'];
        }
        $form->classe->addMultiOptions($classe);

        $teachers = new Administrador_Model_Teachers();
        $getTeachers = $teachers->fetchAll($teachers->select()->from('teachers', array('id', 'name')));
        $teacher = array();
        foreach ($getTeachers as $valueTeachers) {
            $teacher[$valueTeachers['id']] = $valueTeachers['name'];
        }

        $form->teacher->addMultiOptions($teacher);

        $courses = new Administrador_Model_Courses();
        $getCourses = $courses->fetchAll($courses->select()->from('courses', array('id', 'name')));
        $course = array();
        foreach ($getCourses as $valueCourses) {
            $course[$valueCourses['id']] = $valueCourses['name'];
        }

        $form->course->addMultiOptions($course);

        $disciplines = new Administrador_Model_Disciplines();
        $getDiscipline = $disciplines->fetchAll($disciplines->select()->from('disciplines', array('id', 'name')));
        $discipline = array();
        foreach ($getDiscipline as $valueDiscipline) {
            $discipline[$valueDiscipline['id']] = $valueDiscipline['name'];
        }

        $form->discipline->addMultiOptions($discipline);

        $form->setAction($this->view->url(array('module' => 'administrador', 'controller' => 'relatorios', 'action' => 'imprimir-relatorio')));
        $this->view->form = $form;
    }

    public function aprovadosAction() {
        $model = new Administrador_Model_Students();
        $results = $model->fetchAll($model->select()
                        ->setIntegrityCheck(false)
                        ->from(array('A' => 'students'), array('id', 'name', 'username', 'mail', 'status', 'situation'))
                        ->joinLeft(array('B' => 'courses'), "A.courses = B.id", array('courses_id' => 'id', 'courses_name' => 'name'))
                        ->where("situation='1'"));
        $this->view->results = $results;
    }

    public function certificadoAction() {
        // action body
    }

    public function gerarCertificadoAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {

            $certificates = new Administrador_Model_Certificates();
            $getCertificates = $certificates->fetchRow("student='" . $parametros['id'] . "'");

            if (!$getCertificates) {
                $value = array('student' => $parametros['id'],
                    'control' => rand(1000000, 9999999999999),
                    'date' => date("Y-m-d H:i:s"));
                $certificates->insert($value);
            }

            $model = new Administrador_Model_Students();
            $results = $model->fetchRow($model->select()
                            ->setIntegrityCheck(false)
                            ->from(array('A' => 'students'), array('*'))
                            ->joinLeft(array('B' => 'certificates'), "B.student = A.id", array('control'))
                            ->joinLeft(array('C' => 'courses'), "C.id = A.courses", array('courses_name' => 'name'))
                            ->where("A.id='" . $parametros['id'] . "'"));
            $this->view->results = $results;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um aluno para gerar o certificado!'));
            return $this->_helper->redirector->gotoSimple('aprovados', 'relatorios', 'administrador');
        }
    }

    public function imprimirRelatorioAction() {

        $courses = new Administrador_Model_Courses();
        $getCourses = $courses->fetchRow("id='" . $_POST['course'] . "'");
        $this->view->courses = $getCourses;

        $disciplines = new Administrador_Model_Disciplines();
        $getDisciplines = $disciplines->fetchRow("id='" . $_POST['discipline'] . "'");
        $this->view->disciplines = $getDisciplines;

        $teachers = new Administrador_Model_Teachers();
        $getTeachers = $teachers->fetchRow("id='" . $_POST['teacher'] . "'");
        $this->view->teachers = $getTeachers;

        $this->view->module = $_POST['module'];
    }

}


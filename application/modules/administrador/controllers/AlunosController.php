<?php

class Administrador_AlunosController extends Zend_Controller_Action {

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
            if (!(in_array(10, $getPermissions))) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Você não tem permissão para acessar esta área!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'administrador');
            }
        }
        
        $this->_helper->layout->setLayout('administrador');
    }

    public function indexAction() {
        $model = new Administrador_Model_Students();
        $results = $model->fetchAll($model->select()
                        ->setIntegrityCheck(false)
                        ->from(array('A' => 'students'), array('id', 'name', 'username', 'mail', 'status', 'situation'))
                        ->joinLeft(array('B' => 'courses'), "A.courses = B.id", array('courses_id' => 'id', 'courses_name' => 'name')));
        $this->view->results = $results;
    }

    public function adicionarAction() {
        $form = new Administrador_Form_Aluno();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $_POST['date'] = date("Y-m-d H:i:s");
                $_POST['password'] = sha1($_POST['password']);
                
                if($_POST['situation'] != 0) {
                    $_POST['conclusion'] = date('m/d/Y');
                }
                
                unset($_POST['send']);
                $students = new Administrador_Model_Students();
                $insert = $students->insert($_POST);
                if ($insert) {
                    return $this->_helper->redirector->gotoSimple('disciplinas', 'alunos', 'administrador', array('id' => $students->getAdapter()->lastInsertId()));
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao cadastrar o aluno!'));
                    return $this->_helper->redirector->gotoSimple('index', 'alunos', 'administrador');
                }
            }
        }

        $model = new Administrador_Model_Courses();
        $courses = $model->fetchAll($model->select()->from('courses', array('id', 'name')));
        
        $course = array();
        foreach ($courses as $value) {
            $course[$value['id']] = $value['name'];
        }
        
        $classes = new Administrador_Model_Classes();
        $getClasses = $classes->fetchAll($classes->select()->from('classes', array('id', 'name')));
        
        $classe = array();
        foreach ($getClasses as $valueClasse) {
            $classe[$valueClasse['id']] = $valueClasse['name'];
        }

        $form->setAction('');
        $form->courses->addMultiOptions($course);
        $form->classes->addMultiOptions($classe);
        $this->view->form = $form;
    }

    public function disciplinasAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {

            $form = new Administrador_Form_AlunoDiscliplinas();

            $student = new Administrador_Model_Students();
            $result = $student->fetchRow($student->select()
                            ->setIntegrityCheck(false)
                            ->from(array('A' => "students"), array('name', 'courses'))
                            ->joinLeft(array('B' => 'courses'), "A.courses = B.id", array('course' => 'name'))
                            ->where("A.id='" . $parametros['id'] . "'"));

            $model = new Administrador_Model_Disciplines();
            $results = $model->fetchAll($model->select()->from('disciplines', array('id', 'name'))->where("course='" . $result->courses . "'"));

            if ($this->getRequest()->isPost()) {
                $_POST['disciplines_1'] = implode(",", $_POST['disciplines_1']);
                $_POST['disciplines_2'] = implode(",", $_POST['disciplines_2']);
                $_POST['disciplines_3'] = implode(",", $_POST['disciplines_3']);
                $_POST['disciplines_4'] = implode(",", $_POST['disciplines_4']);
                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {
                    $values = array(
                        'disciplines_1' => $form->disciplines_1->getValue(),
                        'disciplines_2' => $form->disciplines_2->getValue(),
                        'disciplines_3' => $form->disciplines_3->getValue(),
                        'disciplines_4' => $form->disciplines_4->getValue(),
                    );
                    $insert = $student->update($values, "id='" . $parametros['id'] . "'");
                    if ($insert) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Disciplinas inseridas com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('index', 'alunos', 'administrador');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao cadastrar as disciplinas!'));
                        return $this->_helper->redirector->gotoSimple('index', 'alunos', 'administrador');
                    }
                }
            }

            $disciplines = array();
            foreach ($results as $value) {
                $disciplines[$value['id']] = $value['name'];
            }

            $form->disciplines_1->addMultiOptions($disciplines);
            $form->disciplines_2->addMultiOptions($disciplines);
            $form->disciplines_3->addMultiOptions($disciplines);
            $form->disciplines_4->addMultiOptions($disciplines);
            $this->view->form = $form;
            $this->view->result = $result;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um aluno para adicionar disciplinas!'));
            return $this->_helper->redirector->gotoSimple('index', 'alunos', 'administrador');
        }
    }

    public function editarAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {

            $form = new Administrador_Form_Aluno();
            $model = new Administrador_Model_Students();
            $results = $model->fetchRow($model->select()
                            ->from("students", array('name',
                                'mail',
                                'status',
                                'identity',
                                'cpf',
                                'birth',
                                'racial',
                                'sex',
                                'nationality',
                                'naturalness',
                                'state',
                                'phone',
                                'needs',
                                'needs_details',
                                'father',
                                'job_father',
                                'mother',
                                'job_mother',
                                'responsible',
                                'responsible_parent',
                                'address',
                                'number',
                                'complement',
                                'neighborhood',
                                'city',
                                'state_address',
                                'cep',
                                'classes',
                                'situation',
                                'username',
                                'conclusion',
                                'password',
                                'courses'))
                            ->where("id='" . $parametros['id'] . "'"));

            if ($this->getRequest()->isPost()) {

                $_POST['username'] = $results->username;
                $_POST['courses'] = $results->courses;
                

                if (empty($_POST['password'])) {
                    $_POST['password'] = $results->password;
                } else {
                    $_POST['password'] = sha1($_POST['password']);
                }

                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {

                    if ($_POST['situation'] != 0 and empty($_POST['conclusion'])) {
                        $_POST['conclusion'] = date('d/m/Y');
                    }

                    $_POST['date'] = date("Y-m-d H:i:s");

                    unset($_POST['send']);
                    $students = new Administrador_Model_Students();
                    $update = $students->update($_POST, "id='" . $parametros['id'] . "'");
                    if ($update) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Aluno editado com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('index', 'alunos', 'administrador');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao cadastrar o aluno!'));
                        return $this->_helper->redirector->gotoSimple('index', 'alunos', 'administrador');
                    }
                }
            }

            $form->name->setValue($results->name);
            $form->conclusion->setValue($results->conclusion);
            $form->situation->setValue($results->situation);
            $form->mail->setValue($results->mail);
            $form->identity->setValue($results->identity);
            $form->cpf->setValue($results->cpf);
            $form->birth->setValue($results->birth);
            $form->racial->setValue($results->racial);
            $form->sex->setValue($results->sex);
            $form->nationality->setValue($results->nationality);
            $form->naturalness->setValue($results->naturalness);
            $form->state->setValue($results->state);
            $form->phone->setValue($results->phone);
            $form->needs->setValue($results->needs);
            $form->needs_details->setValue($results->needs_details);
            $form->father->setValue($results->father);
            $form->job_father->setValue($results->job_father);
            $form->mother->setValue($results->mother);
            $form->job_mother->setValue($results->job_mother);
            $form->responsible->setValue($results->responsible);
            $form->responsible_parent->setValue($results->responsible_parent);
            $form->address->setValue($results->address);
            $form->number->setValue($results->number);
            $form->complement->setValue($results->complement);
            $form->neighborhood->setValue($results->neighborhood);
            $form->city->setValue($results->city);
            $form->state_address->setValue($results->state_address);
            $form->cep->setValue($results->cep);
            $form->status->setValue($results->status);
            $form->username->setValue($results->username)->setAttrib("disable", "disable")->setIgnore(true);
            $form->password->setAttrib("class", "");
            
            $form->send->setLabel("Editar Aluno");

            $course = new Administrador_Model_Courses();
            $courses = $course->fetchAll($course->select()->from('courses', array('id', 'name')));

            $array = array();
            foreach ($courses as $value) {
                $array[$value['id']] = $value['name'];
            }

            $form->courses->setValue($results->courses)->addMultiOptions($array)->setAttrib("disable", "disable");

            $classes = new Administrador_Model_Classes();
            $getClasses = $classes->fetchAll($classes->select()->from('classes', array('id', 'name')));

            $classe = array();
            foreach ($getClasses as $valueClasses) {
                $classe[$valueClasses['id']] = $valueClasses['name'];
            }
            
            $form->classes->setValue($results->classes)->addMultiOptions($classe);
            
            $this->view->form = $form;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um aluno para editar!'));
            return $this->_helper->redirector->gotoSimple('index', 'alunos', 'administrador');
        }
    }

    public function editarDisciplinasAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {
            $student = new Administrador_Model_Students();
            $result = $student->fetchRow($student->select()
                            ->setIntegrityCheck(false)
                            ->from(array('A' => "students"), array('name', 'courses', 'disciplines_1', 'disciplines_2', 'disciplines_3', 'disciplines_4'))
                            ->joinLeft(array('B' => 'courses'), "A.courses = B.id", array('course' => 'name'))
                            ->where("A.id='" . $parametros['id'] . "'"));

            $form = new Administrador_Form_AlunoDiscliplinas();

            $model = new Administrador_Model_Disciplines();
            $results = $model->fetchAll($model->select()->from('disciplines', array('id', 'name'))->where("course='" . $result->courses . "'"));

            if ($this->getRequest()->isPost()) {
                $_POST['disciplines_1'] = implode(",", $_POST['disciplines_1']);
                $_POST['disciplines_2'] = implode(",", $_POST['disciplines_2']);
                $_POST['disciplines_3'] = implode(",", $_POST['disciplines_3']);
                $_POST['disciplines_4'] = implode(",", $_POST['disciplines_4']);
                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {
                    $values = array(
                        'disciplines_1' => $form->disciplines_1->getValue(),
                        'disciplines_2' => $form->disciplines_2->getValue(),
                        'disciplines_3' => $form->disciplines_3->getValue(),
                        'disciplines_4' => $form->disciplines_4->getValue(),
                    );
                    $insert = $student->update($values, "id='" . $parametros['id'] . "'");
                    if ($insert) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Disciplinas editadas com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('index', 'alunos', 'administrador');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao editar as disciplinas!'));
                        return $this->_helper->redirector->gotoSimple('index', 'alunos', 'administrador');
                    }
                }
            }

            $disciplines = array();
            foreach ($results as $value) {
                $disciplines[$value['id']] = $value['name'];
            }

            $disciplines_1 = explode(",", $result->disciplines_1);
            $disciplines_2 = explode(",", $result->disciplines_2);
            $disciplines_3 = explode(",", $result->disciplines_3);
            $disciplines_4 = explode(",", $result->disciplines_4);

            $form->disciplines_1->setValue($disciplines_1)->addMultiOptions($disciplines);
            $form->disciplines_2->setValue($disciplines_2)->addMultiOptions($disciplines);
            $form->disciplines_3->setValue($disciplines_3)->addMultiOptions($disciplines);
            $form->disciplines_4->setValue($disciplines_4)->addMultiOptions($disciplines);
            $this->view->form = $form;
            $this->view->result = $result;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um aluno para editar disciplinas!'));
            return $this->_helper->redirector->gotoSimple('index', 'alunos', 'administrador');
        }
    }

}


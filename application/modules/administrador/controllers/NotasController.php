<?php

class Administrador_NotasController extends Zend_Controller_Action {

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
            if (!(in_array(4, $getPermissions))) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Você não tem permissão para acessar esta área!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'administrador');
            }
        }
        
        $this->_helper->layout->setLayout('administrador');
    }

    public function indexAction() {
        $this->_helper->redirector->gotoSimple('lancar', 'notas', 'administrador');
    }

    public function lancarAction() {
        
        $model = new Administrador_Model_Students();
        $results = $model->fetchAll($model->select()
                        ->setIntegrityCheck(false)
                        ->from(array('A' => 'students'), array('id', 'name'))
                        ->joinLeft(array('B' => 'courses'), "A.courses = B.id", array('course_name' => 'name')));
        $this->view->results = $results;
    }

    public function boletimAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {
            $model = new Administrador_Model_Students();
            $result = $model->fetchRow($model->select()
                            ->setIntegrityCheck(false)
                            ->from(array('A' => 'students'), array('id', 'name', 'disciplines_1', 'disciplines_2', 'disciplines_3', 'disciplines_4'))
                            ->joinLeft(array('B' => 'courses'), "A.courses = B.id", array('course_name' => 'name'))
                            ->where("A.id='" . $parametros['id'] . "'"));

            $disciplines = new Administrador_Model_Disciplines();

            $grades = new Administrador_Model_Grades();

            if ($this->getRequest()->isPost()) {
                $discipline = $grades->fetchRow($grades->select()->from('grades')->where("discipline='" . $_POST['discipline'] . "' AND module='" . $_POST['module'] . "'"));
                if (!($discipline)) {
                    $values = array(
                        'student' => $parametros['id'],
                        'discipline' => $_POST['discipline'],
                        'module' => $_POST['module'],
                        'n_1' => $_POST['n_1'],
                        'n_2' => $_POST['n_2'],
                        'f_n' => $_POST['f'],
                        'f_j' => $_POST['f_j'],
                        'n_r' => $_POST['n_r'],
                        'situation' => $_POST['situation'],
                        'appraiser' => 1,
                        'date' => date('Y-m-d H:i:s'),
                    );
                    $insert = $grades->insert($values);
                    if ($insert) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'A nota foi lançada com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('boletim', 'notas', 'administrador', array('id' => $parametros['id'], 'module' => $_POST['module']));
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao tentar lançar a nota!'));
                        return $this->_helper->redirector->gotoSimple('boletim', 'notas', 'administrador', array('id' => $parametros['id'], 'module' => $_POST['module']));
                    }
                } else {
                    $values = array(
                        'student' => $parametros['id'],
                        'discipline' => $_POST['discipline'],
                        'module' => $_POST['module'],
                        'n_1' => $_POST['n_1'],
                        'n_2' => $_POST['n_2'],
                        'f_n' => $_POST['f'],
                        'f_j' => $_POST['f_j'],
                        'n_r' => $_POST['n_r'],
                        'situation' => $_POST['situation'],
                        'appraiser' => 1,
                        'date' => date('Y-m-d H:i:s'),
                    );
                    $update = $grades->update($values, "id='" . $discipline->id . "'");
                    if ($update) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'A nota foi lançada com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('boletim', 'notas', 'administrador', array('id' => $parametros['id'], 'module' => $_POST['module']));
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao tentar lançar a nota!'));
                        return $this->_helper->redirector->gotoSimple('boletim', 'notas', 'administrador', array('id' => $parametros['id'], 'module' => $_POST['module']));
                    }
                }
            }

            $select = $disciplines->select()->from(array('A' => "disciplines"), array('disciplines_id' => 'id', 'name'));

            if (!isset($parametros['modulo']) or ($parametros['modulo'] == 1)) {
                $parm = 1;
                $this->view->modulo = 1;
                $disciplines_1 = explode('|', $result->disciplines_1);
                foreach ($disciplines_1 as $value) {
                    $select->orWhere("A.id='" . $value . "'");
                }
            } elseif (isset($parametros['modulo']) and $parametros['modulo'] == 2) {
                $parm = 2;
                $this->view->modulo = 2;
                $disciplines_2 = explode('|', $result->disciplines_2);
                foreach ($disciplines_2 as $value) {
                    $select->orWhere("A.id='" . $value . "'");
                }
            } elseif (isset($parametros['modulo']) and $parametros['modulo'] == 3) {
                $parm = 3;
                $this->view->modulo = 3;
                $disciplines_3 = explode('|', $result->disciplines_3);
                foreach ($disciplines_3 as $value) {
                    $select->orWhere("A.id='" . $value . "'");
                }
            } elseif (isset($parametros['modulo']) and $parametros['modulo'] == 4) {
                $parm = 4;
                $this->view->modulo = 4;
                $disciplines_4 = explode('|', $result->disciplines_4);
                foreach ($disciplines_4 as $value) {
                    $select->orWhere("A.id='" . $value . "'");
                }
            }

            $get = $disciplines->fetchAll($select->joinLeft(array('B' => 'grades'), "A.id = B.discipline AND B.module= " . $parm)->setIntegrityCheck(false));

            $this->view->result = $result;
            $this->view->get = $get;
            $this->view->parm = $parm;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um aluno para lançar nota!'));
            return $this->_helper->redirector->gotoSimple('lancar', 'notas', 'administrador');
        }
    }

    public function imprimirAction() {

        $this->_helper->layout->disableLayout();
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {
            $model = new Administrador_Model_Students();
            $student = $model->fetchRow($model->select()
                            ->setIntegrityCheck(false)
                            ->from(array('A' => 'students'), array('id', 'name', 'disciplines_1', 'disciplines_2', 'disciplines_3', 'disciplines_4'))
                            ->joinLeft(array('B' => 'courses'), "A.courses = B.id", array('course_name' => 'name'))
                            ->where("A.id = '" . $parametros['id'] . "'"));
            
            $disciplines = new Administrador_Model_Disciplines();
            
            $select = $disciplines->select()->from(array('A' => "disciplines"), array('disciplines_id' => 'id', 'name'));

            if (!isset($parametros['modulo']) or ($parametros['modulo'] == 1)) {
                $parm = 1;
                $this->view->modulo = 1;
                $disciplines_1 = explode('|', $student->disciplines_1);
                foreach ($disciplines_1 as $value) {
                    $select->orWhere("A.id='" . $value . "'");
                }
            } elseif (isset($parametros['modulo']) and $parametros['modulo'] == 2) {
                $parm = 2;
                $this->view->modulo = 2;
                $disciplines_2 = explode('|', $student->disciplines_2);
                foreach ($disciplines_2 as $value) {
                    $select->orWhere("A.id='" . $value . "'");
                }
            } elseif (isset($parametros['modulo']) and $parametros['modulo'] == 3) {
                $parm = 3;
                $this->view->modulo = 3;
                $disciplines_3 = explode('|', $student->disciplines_3);
                foreach ($disciplines_3 as $value) {
                    $select->orWhere("A.id='" . $value . "'");
                }
            } elseif (isset($parametros['modulo']) and $parametros['modulo'] == 4) {
                $parm = 4;
                $this->view->modulo = 4;
                $disciplines_4 = explode('|', $student->disciplines_4);
                foreach ($disciplines_4 as $value) {
                    $select->orWhere("A.id='" . $value . "'");
                }
            }

            $get = $disciplines->fetchAll($select->joinLeft(array('B' => 'grades'), "A.id = B.discipline AND B.module= " . $parm)->setIntegrityCheck(false));

            $this->view->result  = $get;
            
            $this->view->student = $student;
            $this->view->module  = $parm;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um aluno para imprimir o boletim!'));
            return $this->_helper->redirector->gotoSimple('lancar', 'notas', 'administrador');
        }
    }

}


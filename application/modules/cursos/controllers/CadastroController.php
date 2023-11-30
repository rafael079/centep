<?php

class Cursos_CadastroController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $parametros = $this->getAllParams();

        if (isset($parametros['curso'])) {
            $this->view->curso = $parametros['curso'];
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um curso para se matricular!'));
            return $this->_helper->redirector->gotoSimple('index', 'index', 'cursos');
        }
    }

    public function proximoAction()
    {
        $parametros = $this->getAllParams();
        $form = new Cursos_Form_Signup();
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                
                $students = new Administrador_Model_Students();
                $validator = $students->fetchRow("username='" . $_POST['username'] . "'");
                if($validator) {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'O Username escolhido já existe, por favor selecione outro!'));
                    return $this->_helper->redirector->gotoSimple('proximo', 'cadastro', 'cursos', array('curso' => $_POST['courses']));
                }
                
                $_POST['date'] = date("Y-m-d H:i:s");
                $_POST['password'] = sha1($_POST['password']);
                $_POST['status'] = 0;
                unset($_POST['send']);
                
                $insert = $students->insert($_POST);
                if ($insert) {
                    $this->_helper->flashMessenger->addMessage(array('seucess' => 'Seu cadastro foi enviado com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'index', 'login');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao cadastrar o aluno!'));
                    return $this->_helper->redirector->gotoSimple('index', 'alunos', 'administrador');
                }
            }
        }
        
        $courses = new Administrador_Model_Courses();
        $result = $courses->fetchAll($courses->select()->from('courses', array('id', 'name')));
        $array = array();
        foreach ($result->toArray() as $value) {
            $array[$value['id']] = $value['name'];
        }
        $form->courses->addMultiOptions($array);
        if (isset($parametros['curso'])) {
            $form->courses->setValue($parametros['curso']);
        }
        $this->view->form = $form;
    }

    public function finalizarAction()
    {
        // action body
    }


}




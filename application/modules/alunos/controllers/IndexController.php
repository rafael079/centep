<?php

class Alunos_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $this->auth = Zend_Auth::getInstance();
        $this->auth->setStorage(new Zend_Auth_Storage_Session('User_Aluno'));
        if (!$this->auth->hasIdentity()) {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Efetue o login para acessar esta área!'));
            $this->_helper->redirector->gotoSimple('index', 'index', 'login');
        }
    }

    public function indexAction()
    {
        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;
    }

    public function dadosAction()
    {
        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;
        $students = new Administrador_Model_Students();
        $results = $students->fetchRow("id='" . $user->id . "'");
        $this->view->results = $results;

        $form = new Alunos_Form_Dados();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {

                $values = array(
                    'name' => $form->name->getValue(),
                    'mail' => $form->mail->getValue(),
                );

                if (!empty($_POST['password'])) {
                    $values['password'] = sha1($form->password->getValue());
                }

                $update = $students->update($values, "id='" . $user->id . "'");

                if ($update) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Dados atualizados com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'index', 'alunos');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao atualizar seus dados. Tente mais tarde!'));
                    return $this->_helper->redirector->gotoSimple('dados', 'index', 'alunos');
                }
            }
        }

        $form->name->setValue($results->name);
        $form->mail->setValue($results->mail);
        $this->view->form = $form;
    }

    public function perguntasAction()
    {

        $parametros = $this->getAllParams();

        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;

        $form = new Alunos_Form_Questions();

        $questions = new Alunos_Model_Questions();
        $lists = $questions->fetchAll(
                $questions->select()
                        ->setIntegrityCheck(FALSE)
                        ->from(array('A' => 'questions', array('id', 'message', 'reply', 'date', 'reply_date')))
                        ->joinLeft(array('B' => 'teachers'), "A.teacher = B.id", array('name'))
                        ->where("A.student='" . $user->id . "'"));
        $this->view->lists = $lists;

        if (isset($parametros['excluir'])) {
            $delete = $questions->delete("id='" . $parametros['excluir'] . "'");
            if ($delete) {
                $this->_helper->flashMessenger->addMessage(array('success' => 'Mensagem excluida com sucesso!'));
                return $this->_helper->redirector->gotoSimple('perguntas', 'index', 'alunos');
            } else {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao deletar sua mensagem. Tente mais tarde!'));
                return $this->_helper->redirector->gotoSimple('perguntas', 'index', 'alunos');
            }
        }

        $students = new Administrador_Model_Students();
        $results = $students->fetchRow("id='" . $user->id . "'");
        $this->view->results = $results;

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $datas = array(
                    'student' => $user->id,
                    'teacher' => $form->teacher->getValue(),
                    'message' => $form->message->getValue(),
                    'answered' => 0,
                    'date' => date("Y-m-d H:i:s")
                );
                $insert = $questions->insert($datas);
                if ($insert) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Sua mensagem foi enviado com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('perguntas', 'index', 'alunos');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao enviar a sua mensagem. Tente mais tarde!'));
                    return $this->_helper->redirector->gotoSimple('perguntas', 'index', 'alunos');
                }
            }
        }

        $teacher = new Administrador_Model_Teachers();
        $values = $teacher->fetchAll($teacher->select()
                        ->from('teachers', array('id', 'name'))
                        ->where("courses='" . $user->courses . "'"));

        $array = array();
        foreach ($values->toArray() as $data) {
            $array[$data['id']] = $data['name'];
        }

        $form->teacher->addMultiOptions($array);
        $this->view->form = $form;
    }

    public function sairAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        return $this->_helper->redirector->gotoSimple('index', 'index', 'login');
    }

    public function arquivosAction()
    {
        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;
        
        $model = new Alunos_Model_Archives();
        $results = $model->fetchAll(
                $model->select()
                ->setIntegrityCheck(FALSE)
                ->from(array('A' => 'archives'), array('id', 'description', 'file', 'date'))
                ->joinLeft(array('B' => 'teachers'), "A.teacher = B.id", array('id', 'name'))
                ->where("B.courses = '" . $user->courses . "'"));
        $this->view->results = $results;
    }

    public function boletimAction()
    {
        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;
        
        $model = new Administrador_Model_Grades();
        $mod1 = $model->fetchAll($model->select()
                ->setIntegrityCheck(false)
                ->from(array('A' => 'grades'), array('n_1', 'n_2', 'f_n', 'f_j', 'n_r', 'situation'))
                ->joinLeft(array('B' => 'disciplines'), "B.id = A.discipline", array('discipline' => 'name'))
                ->where("A.module = '1'")
                ->where("A.student = '" . $user->courses . "'"));
        $this->view->mod1 = $mod1;
        
        $mod2 = $model->fetchAll($model->select()
                ->setIntegrityCheck(false)
                ->from(array('A' => 'grades'), array('n_1', 'n_2', 'f_n', 'f_j', 'n_r', 'situation'))
                ->joinLeft(array('B' => 'disciplines'), "B.id = A.discipline", array('discipline' => 'name'))
                ->where("A.module = '2'")
                ->where("A.student = '" . $user->courses . "'"));
        $this->view->mod2 = $mod2;
        
        $mod3 = $model->fetchAll($model->select()
                ->setIntegrityCheck(false)
                ->from(array('A' => 'grades'), array('n_1', 'n_2', 'f_n', 'f_j', 'n_r', 'situation'))
                ->joinLeft(array('B' => 'disciplines'), "B.id = A.discipline", array('discipline' => 'name'))
                ->where("A.module = '3'")
                ->where("A.student = '" . $user->courses . "'"));
        $this->view->mod3 = $mod3;
        
        $mod4 = $model->fetchAll($model->select()
                ->setIntegrityCheck(false)
                ->from(array('A' => 'grades'), array('n_1', 'n_2', 'f_n', 'f_j', 'n_r', 'situation'))
                ->joinLeft(array('B' => 'disciplines'), "B.id = A.discipline", array('discipline' => 'name'))
                ->where("A.module = '4'")
                ->where("A.student = '" . $user->courses . "'"));
        $this->view->mod4 = $mod4;
    }


}




<?php

class Professores_IndexController extends Zend_Controller_Action {

    public function init() {
        $this->auth = Zend_Auth::getInstance();
        $this->auth->setStorage(new Zend_Auth_Storage_Session('User_Professor'));
        if (!$this->auth->hasIdentity()) {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Efetue o login para acessar esta área!'));
            $this->_helper->redirector->gotoSimple('index', 'index', 'login');
        }
    }

    public function indexAction() {
        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;

        $teachers = new Administrador_Model_Teachers();
        $results = $teachers->fetchRow("id='" . $user->id . "'");
        $this->view->results = $results;

        $model = new Professores_Model_Time();

        date_default_timezone_set('America/Sao_Paulo');

        $times = $model->fetchRow($model->select()->from("teachers_time", array('id', 'entry', 'exit'))->where("teachers='" . $user->id . "' AND date='" . date('Y-m-d') . "'"));

        $this->view->entry = $times->entry;
        $this->view->exit = $times->exit;
    }

    public function dadosAction() {
        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;

        $teachers = new Administrador_Model_Teachers();
        $results = $teachers->fetchRow("id='" . $user->id . "'");
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

                $update = $teachers->update($values, "id='" . $user->id . "'");

                if ($update) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Dados atualizados com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'index', 'professores');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao atualizar seus dados. Tente mais tarde!'));
                    return $this->_helper->redirector->gotoSimple('dados', 'index', 'professores');
                }
            }
        }

        $form->name->setValue($results->name);
        $form->mail->setValue($results->mail);
        $this->view->form = $form;

        $model = new Professores_Model_Time();

        date_default_timezone_set('America/Sao_Paulo');

        $times = $model->fetchRow($model->select()->from("teachers_time", array('id', 'entry', 'exit'))->where("teachers='" . $user->id . "' AND date='" . date('Y-m-d') . "'"));

        $this->view->entry = $times->entry;
        $this->view->exit = $times->exit;
    }

    public function sairAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        return $this->_helper->redirector->gotoSimple('index', 'index', 'login');
    }

    public function perguntasAction() {
        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;

        $questions = new Alunos_Model_Questions();
        $lists = $questions->fetchAll(
                $questions->select()
                        ->setIntegrityCheck(FALSE)
                        ->from(array('A' => 'questions', array('id', 'message', 'reply', 'date', 'reply_date')))
                        ->joinLeft(array('B' => 'students'), "A.student = B.id", array('name'))
                        ->where("A.teacher='" . $user->id . "'"));
        $this->view->lists = $lists;

        $teachers = new Administrador_Model_Teachers();
        $results = $teachers->fetchRow("id='" . $user->id . "'");
        $this->view->results = $results;

        $model = new Professores_Model_Time();

        date_default_timezone_set('America/Sao_Paulo');

        $times = $model->fetchRow($model->select()->from("teachers_time", array('id', 'entry', 'exit'))->where("teachers='" . $user->id . "' AND date='" . date('Y-m-d') . "'"));

        $this->view->entry = $times->entry;
        $this->view->exit = $times->exit;
    }

    public function responderAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {

            $questions = new Alunos_Model_Questions();
            $question = $questions->fetchRow($questions->select()
                            ->setIntegrityCheck(FALSE)
                            ->from(array('A' => 'questions'), array('message', 'reply'))
                            ->joinLeft(array('B' => 'students'), "B.id = A.student", array('name'))
                            ->where("A.id='" . $parametros['id'] . "'"));
            $this->view->question = $question;

            $user = Zend_Auth::getInstance()->getIdentity();
            $this->view->user = $user;

            $teachers = new Administrador_Model_Teachers();
            $results = $teachers->fetchRow("id='" . $user->id . "'");
            $this->view->results = $results;

            $form = new Alunos_Form_Questions();

            if ($this->getRequest()->isPost()) {
                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {

                    $values = array(
                        'reply' => $form->message->getValue(),
                        'reply_date' => date("Y-m-d H:i:s"),
                        'answered' => 1,
                    );

                    $update = $questions->update($values, "id='" . $parametros['id'] . "'");
                    if ($update) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Resposta enviado com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('perguntas', 'index', 'professores');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao enviar sua resposta, tente mais tarde!'));
                        return $this->_helper->redirector->gotoSimple('perguntas', 'index', 'professores');
                    }
                }
            }
            $form->message->setValue($question->reply);
            $this->view->form = $form;

            $model = new Professores_Model_Time();

            date_default_timezone_set('America/Sao_Paulo');

            $times = $model->fetchRow($model->select()->from("teachers_time", array('id', 'entry', 'exit'))->where("teachers='" . $user->id . "' AND date='" . date('Y-m-d') . "'"));

            $this->view->entry = $times->entry;
            $this->view->exit = $times->exit;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione uma pergunta para responde-la!'));
            return $this->_helper->redirector->gotoSimple('perguntas', 'index', 'professores');
        }
    }

    public function arquivosAction() {

        $parametros = $this->getAllParams();

        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;

        $teachers = new Administrador_Model_Teachers();
        $results = $teachers->fetchRow("id='" . $user->id . "'");
        $this->view->results = $results;

        $model = new Alunos_Model_Archives();
        $lists = $model->fetchAll("teacher='" . $user->id . "'");
        $this->view->lists = $lists;

        if (isset($parametros['excluir'])) {
            $delete = $model->delete("id='" . $parametros['excluir'] . "'");
            if ($delete) {
                $this->_helper->flashMessenger->addMessage(array('seccess' => 'Arquivo excluido com sucesso!'));
                return $this->_helper->redirector->gotoSimple('arquivos', 'index', 'professores');
            } else {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao deletar o arquivo, tente mais tarde!'));
                return $this->_helper->redirector->gotoSimple('arquivos', 'index', 'professores');
            }
        }

        if ($this->getRequest()->isPost()) {

            if (empty($_POST['description'])) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Informe uma descrição para o arquivo!'));
                return $this->_helper->redirector->gotoSimple('arquivos', 'index', 'professores');
            } elseif (empty($_FILES['file']['name'])) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione o arquivo!'));
                return $this->_helper->redirector->gotoSimple('arquivos', 'index', 'professores');
            }

            $arquivo = $_FILES['file'];
            $diretorio = 'E:\Servidor\htdocs\centep\public\uploads\archives';
            preg_match("/\.(jpg|jpeg|png|gif|rar|zip|pdf|doc|docx|txt|html|htm|xls|pps|ppsx|ppt){1}$/i", $arquivo["name"], $ext);
            $nome_arquivo = md5(uniqid(time())) . "." . $ext[1];

            $caminho_imagem = $diretorio . '/' . $nome_arquivo;

            if (move_uploaded_file($arquivo["tmp_name"], $caminho_imagem)) {
                $values = array(
                    'teacher' => $user->id,
                    'description' => trim(strip_tags($_POST['description'])),
                    'file' => $nome_arquivo,
                    'date' => date("Y-m-d H:i:s"),
                );
                $insert = $model->insert($values);
                if ($insert) {
                    $this->_helper->flashMessenger->addMessage(array('seccess' => 'Arquivo enviado com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('arquivos', 'index', 'professores');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Ocorreu um erro ao tentar enviar o arquivo, tente mais tarde!'));
                    return $this->_helper->redirector->gotoSimple('arquivos', 'index', 'professores');
                }
            } else {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Ocorreu um erro ao tentar enviar o arquivo, tente mais tarde!'));
                return $this->_helper->redirector->gotoSimple('arquivos', 'index', 'professores');
            }
        }

        $model = new Professores_Model_Time();

        date_default_timezone_set('America/Sao_Paulo');

        $times = $model->fetchRow($model->select()->from("teachers_time", array('id', 'entry', 'exit'))->where("teachers='" . $user->id . "' AND date='" . date('Y-m-d') . "'"));

        $this->view->entry = $times->entry;
        $this->view->exit = $times->exit;
    }

    public function boletimAction() {
        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;

        $teachers = new Administrador_Model_Teachers();
        $results = $teachers->fetchRow("id='" . $user->id . "'");
        $this->view->results = $results;

        $model = new Administrador_Model_Students();
        $students = $model->fetchAll($model->select()
                        ->setIntegrityCheck(false)
                        ->from(array('A' => 'students'), array('id', 'name'))
                        ->joinLeft(array('B' => 'courses'), "A.courses = B.id", array('course_name' => 'name'))
                        ->where("B.id = '" . $user->courses . "'"));
        $this->view->students = $students;

        $model = new Professores_Model_Time();

        date_default_timezone_set('America/Sao_Paulo');

        $times = $model->fetchRow($model->select()->from("teachers_time", array('id', 'entry', 'exit'))->where("teachers='" . $user->id . "' AND date='" . date('Y-m-d') . "'"));

        $this->view->entry = $times->entry;
        $this->view->exit = $times->exit;
    }

    public function lancarAction() {
        $parametros = $this->getAllParams();

        if ($parametros['aluno']) {
            $user = Zend_Auth::getInstance()->getIdentity();
            $this->view->user = $user;

            $teachers = new Administrador_Model_Teachers();
            $results = $teachers->fetchRow("id='" . $user->id . "'");
            $this->view->results = $results;

            $model = new Administrador_Model_Students();
            $students = $model->fetchRow($model->select()
                            ->setIntegrityCheck(false)
                            ->from(array('A' => 'students'), array('id', 'name', 'disciplines_1', 'disciplines_2', 'disciplines_3', 'disciplines_4'))
                            ->joinLeft(array('B' => 'courses'), "A.courses = B.id", array('course_id' => 'id', 'course_name' => 'name'))
                            ->where("A.id = '" . $parametros['aluno'] . "'"));
            $this->view->students = $students;

            $disciplines = new Administrador_Model_Disciplines();
            $selectDisciplines = $disciplines->select()->from(array('A' => "disciplines"), array('disciplines_id' => 'id', 'name'));

            $grades = new Administrador_Model_Grades();

            if ($this->getRequest()->isPost()) {
                $discipline = $grades->fetchRow($grades->select()->from('grades')->where("discipline='" . $_POST['discipline'] . "' AND module='" . $_POST['module'] . "'"));
                if (!($discipline)) {
                    $values = array(
                        'student' => $parametros['aluno'],
                        'discipline' => $_POST['discipline'],
                        'module' => $_POST['module'],
                        'n_1' => $_POST['n_1'],
                        'n_2' => $_POST['n_2'],
                        'f_n' => $_POST['f'],
                        'f_j' => $_POST['f_j'],
                        'n_r' => $_POST['n_r'],
                        'situation' => 0,
                        'appraiser' => 1,
                        'date' => date('Y-m-d H:i:s'));
                    $insert = $grades->insert($values);
                    if ($insert) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'A nota foi lançada com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('lancar', 'index', 'professores', array('aluno' => $parametros['aluno']));
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao tentar lançar a nota!'));
                        return $this->_helper->redirector->gotoSimple('lancar', 'index', 'professores', array('aluno' => $parametros['aluno']));
                    }
                } else {
                    $values = array(
                        'student' => $parametros['aluno'],
                        'discipline' => $_POST['discipline'],
                        'module' => $_POST['module'],
                        'n_1' => $_POST['n_1'],
                        'n_2' => $_POST['n_2'],
                        'f_n' => $_POST['f'],
                        'f_j' => $_POST['f_j'],
                        'n_r' => $_POST['n_r'],
                        'situation' => 0,
                        'appraiser' => 1,
                        'date' => date('Y-m-d H:i:s'));
                    $update = $grades->update($values, "id='" . $discipline->id . "'");
                    if ($update) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'A nota foi lançada com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('lancar', 'index', 'professores', array('aluno' => $parametros['aluno']));
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao tentar lançar a nota!'));
                        return $this->_helper->redirector->gotoSimple('lancar', 'index', 'professores', array('aluno' => $parametros['aluno']));
                    }
                }
            }

            if (!isset($parametros['modulo']) or ($parametros['modulo'] == 1)) {
                $parm = 1;
                $this->view->modulo = 1;
                $disciplines_1 = explode('|', $students->disciplines_1);
                foreach ($disciplines_1 as $value) {
                    $selectDisciplines->orWhere("A.id='" . $value . "'");
                }
            } elseif (isset($parametros['modulo']) and $parametros['modulo'] == 2) {
                $parm = 2;
                $this->view->modulo = 2;
                $disciplines_2 = explode('|', $students->disciplines_2);
                foreach ($disciplines_2 as $value) {
                    $selectDisciplines->orWhere("A.id='" . $value . "'");
                }
            } elseif (isset($parametros['modulo']) and $parametros['modulo'] == 3) {
                $parm = 3;
                $this->view->modulo = 3;
                $disciplines_3 = explode('|', $students->disciplines_3);
                foreach ($disciplines_3 as $value) {
                    $selectDisciplines->orWhere("A.id='" . $value . "'");
                }
            } elseif (isset($parametros['modulo']) and $parametros['modulo'] == 4) {
                $parm = 4;
                $this->view->modulo = 4;
                $disciplines_4 = explode('|', $students->disciplines_4);
                foreach ($disciplines_4 as $value) {
                    $selectDisciplines->orWhere("A.id='" . $value . "'");
                }
            }


            $resultsModule = $disciplines->fetchAll($selectDisciplines->joinLeft(array('B' => 'grades'), "A.id = B.discipline")->setIntegrityCheck(false));

            $this->view->resultsModule = $resultsModule;
            $this->view->parm = $parm;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um aluno para lançar nota!'));
            return $this->_helper->redirector->gotoSimple('boletim', 'index', 'professores');
        }

        $model = new Professores_Model_Time();

        date_default_timezone_set('America/Sao_Paulo');

        $times = $model->fetchRow($model->select()->from("teachers_time", array('id', 'entry', 'exit'))->where("teachers='" . $user->id . "' AND date='" . date('Y-m-d') . "'"));

        $this->view->entry = $times->entry;
        $this->view->exit = $times->exit;
    }

    public function pontoAction() {
        $user = Zend_Auth::getInstance()->getIdentity();
        $this->view->user = $user;

        $teachers = new Administrador_Model_Teachers();
        $results = $teachers->fetchRow("id='" . $user->id . "'");
        $this->view->results = $results;

        $model = new Professores_Model_Time();

        date_default_timezone_set('America/Sao_Paulo');

        $locale = new Zend_Locale('pt_BR');
        $dates = new Zend_Date();

        $time = $dates->get(Zend_Date::TIMES, $locale);

        $times = $model->fetchRow($model->select()->from("teachers_time", array('id', 'entry', 'exit'))->where("teachers='" . $user->id . "' AND date='" . date('Y-m-d') . "'"));

        $form = new Professores_Form_Time();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                if (!$times) {
                    $value = array(
                        'date' => date('Y-m-d'),
                        'teachers' => $user->id,
                        'entry' => $time,
                    );
                    $insert = $model->insert($value);
                    if ($insert) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Dados inseridos com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('ponto', 'index', 'professores');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao cadastrar os dados!'));
                        return $this->_helper->redirector->gotoSimple('ponto', 'index', 'professores');
                    }
                } else {
                    $value = array(
                        'date' => date('Y-m-d'),
                        'teachers' => $user->id,
                        'exit' => $time,
                    );
                    $update = $model->update($value, "id='" . $times->id . "'");
                    if ($update) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Dados inseridos com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('ponto', 'index', 'professores');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao cadastrar os dados!'));
                        return $this->_helper->redirector->gotoSimple('ponto', 'index', 'professores');
                    }
                }
            }
        }

        if ($times->entry == '00:00:00' or is_null($times->entry)) {
            $form->entry->setValue($time);
        } else {
            $form->entry->setValue($times->entry)->setAttrib('disabled', 'disabled');
        }

        if ($times->exit == '00:00:00' or is_null($times->exit)) {
            $form->exit->setValue($time);
        } else {
            $form->exit->setValue($times->exit)->setAttrib('disabled', 'disabled');
        }

        $this->view->form = $form;
        $this->view->entry = $times->entry;
        $this->view->exit = $times->exit;
    }

}


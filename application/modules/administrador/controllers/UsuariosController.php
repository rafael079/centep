<?php

class Administrador_UsuariosController extends Zend_Controller_Action {

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
            if (!(in_array(2, $getPermissions))) {
                $this->_helper->flashMessenger->addMessage(array('error' => 'Você não tem permissão para acessar esta área!'));
                return $this->_helper->redirector->gotoSimple('index', 'index', 'administrador');
            }
        }
        
        $this->_helper->layout->setLayout('administrador');
    }

    public function indexAction() {
        $model = new Administrador_Model_Users();
        
        $select = $model->select()
                         ->setIntegrityCheck(false)
                         ->from(array('A' => 'users'), 
                                array('id', 'name', 'username', 'mail'))
                         ->joinLeft(array("B" => 'groups'), "A.group = B.id", array('groups' => 'name'));

        $results = $model->fetchAll($select);
        
        $this->view->results = $results;
    }

    public function gruposAction() {
        
        $model = new Administrador_Model_Groups();
        $results = $model->fetchAll($model->select()->from('groups', array('id', 'name', 'permissions')));
        $this->view->results = $results;
        
    }

    public function excluirAction() {

        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {
            
            $model = new Administrador_Model_Users();
            $results = $model->fetchRow($model->select()->from('users', array('id','name'))->where("id='" . $parametros['id'] . "'"));
            $this->view->results = $results;
            
            if (isset($parametros['confirmar']) && $parametros['confirmar'] == 1) {
                $delete = $model->delete("id='" . $parametros['id'] . "'");
                if ($delete) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Usuário excluido com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'usuarios', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao tentar excluir o usuário!'));
                    return $this->_helper->redirector->gotoSimple('index', 'usuarios', 'administrador');
                }
            }
            
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um usuário para excluir!'));
            return $this->_helper->redirector->gotoSimple('index', 'usuarios', 'administrador');
        }
    }

    public function editarAction() {

        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {

            $form = new Administrador_Form_Usuarios();

            $model = new Administrador_Model_Users();
            $results = $model->fetchRow($model->select()->from('users', array('id','name', 'mail', 'username', 'group' ))->where("id='" . $parametros['id'] . "'"));

            if ($this->getRequest()->isPost()) {

                $_POST['username'] = $results->username;

                if (empty($_POST['password'])) {
                    $_POST['password'] = $results->password;
                } else {
                    $_POST['password'] = sha1($_POST['password']);
                }

                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {
                    $values = array(
                        'name' => $form->name->getValue(),
                        'password' => $form->password->getValue(),
                        'mail' => $form->mail->getValue(),
                        'status' => 1,
                        'group' => $form->group->getValue(),
                        'date' => date('Y-m-d H:i:s'),
                    );
                    $update = $model->update($values, "id='" . $parametros['id'] . "'");
                    if ($update) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Usuário editado com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('index', 'usuarios', 'administrador');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao editar o usuário!'));
                        return $this->_helper->redirector->gotoSimple('index', 'usuarios', 'administrador');
                    }
                }
            }

            $group = new Administrador_Model_Groups();
            $groups = $group->fetchAll($group->select()->from('groups', array('id','name')));

            foreach ($groups->toArray() as $values) {
                $array[$values['id']] = $values['name'];
            }

            $form->name->setValue($results->name);
            $form->mail->setValue($results->mail);
            $form->password->setAttrib("class", "");
            $form->username->setValue($results->username)->setIgnore(true)->setAttrib("disable", "disable");
            $form->group->setValue($results->group)->addMultiOptions($array);

            $this->view->form = $form;
            
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um usuário para editar!'));
            return $this->_helper->redirector->gotoSimple('index', 'usuarios', 'administrador');
        }
    }

    public function adicionarAction() {

        $form = new Administrador_Form_Usuarios();

        $model = new Administrador_Model_Users();

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $values = array(
                    'name' => $form->name->getValue(),
                    'username' => $form->username->getValue(),
                    'password' => sha1($form->password->getValue()),
                    'mail' => $form->mail->getValue(),
                    'status' => 1,
                    'group' => $form->group->getValue(),
                    'date' => date('Y-m-d H:i:s'),
                );
                $insert = $model->insert($values);
                if ($insert) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Usuário cadastrados com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'usuarios', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao cadastrar o usuário!'));
                    return $this->_helper->redirector->gotoSimple('index', 'usuarios', 'administrador');
                }
            }
        }

        $group = new Administrador_Model_Groups();
        $groups = $group->fetchAll($group->select()->from('groups', array('id','name')));

        foreach ($groups->toArray() as $values) {
            $array[$values['id']] = $values['name'];
        }

        $form->group->addMultiOptions($array);

        $this->view->form = $form;
    }

    public function adicionarGruposAction() {
        
        $form = new Administrador_Form_Groups();

        if ($this->getRequest()->isPost()) {
            $_POST['permissions'] = implode("|", $_POST['permissions']);
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $values = array(
                    'name' => $form->name->getValue(),
                    'permissions' => $form->permissions->getValue(),
                    'date' => date('Y-m-d H:i:s'),
                );

                $model = new Administrador_Model_Groups();
                $insert = $model->insert($values);

                if ($insert) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Grupo cadastrados com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('grupos', 'usuarios', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao cadastrar o grupo!'));
                    return $this->_helper->redirector->gotoSimple('grupos', 'usuarios', 'administrador');
                }
            }
        }

        $this->view->form = $form;
    }

    public function editarGrupoAction() {

        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {
            $form = new Administrador_Form_Groups();

            $model = new Administrador_Model_Groups();

            if ($this->getRequest()->isPost()) {
                
                $_POST['permissions'] = implode("|", $_POST['permissions']);
                
                $data = $this->getRequest()->getPost();
                if ($form->isValid($data)) {
                    $values = array(
                        'name' => $form->name->getValue(),
                        'permissions' => $form->permissions->getValue(),
                        'date' => date('Y-m-d H:i:s'),
                    );
                    
                    $update = $model->update($values, "id='" . $parametros['id'] . "'");

                    if ($update) {
                        $this->_helper->flashMessenger->addMessage(array('success' => 'Grupo editado com sucesso!'));
                        return $this->_helper->redirector->gotoSimple('grupos', 'usuarios', 'administrador');
                    } else {
                        $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao editar o grupo!'));
                        return $this->_helper->redirector->gotoSimple('grupos', 'usuarios', 'administrador');
                    }
                }
            }

            $results = $model->fetchRow($model->select()->from("groups", array("name", "permissions"))->where("id='" . $parametros['id'] . "'" ));
            $form->name->setValue($results->name);
            $explode = explode("|", $results->permissions);
            $form->permissions->setValue($explode);
            $this->view->form = $form;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um usuário para editar!'));
            return $this->_helper->redirector->gotoSimple('grupos', 'usuarios', 'administrador');
        }
    }

    public function excluirGrupoAction() {

        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {
            
            $model = new Administrador_Model_Groups();
            $results = $model->fetchRow($model->select()->from("groups", array("id","name"))->where("id='" . $parametros['id'] . "'" ));
            $this->view->results = $results;
            
            if (isset($parametros['confirmar']) && ($parametros['confirmar'] == 1)) {
                $delete = $model->delete("id='" . $parametros['id'] . "'");
                if ($delete) {
                    $this->_helper->flashMessenger->addMessage(array('success' => 'Grupo excluido com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('grupos', 'usuarios', 'administrador');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Erro ao exluir o Grupo!'));
                    return $this->_helper->redirector->gotoSimple('grupos', 'usuarios', 'administrador');
                }
            }
            
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um usuário para excluir!'));
            return $this->_helper->redirector->gotoSimple('grupos', 'usuarios', 'administrador');
        }
    }

}

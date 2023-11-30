<?php

class Default_NoticiasController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione uma noticia para ler!'));
        return $this->_helper->redirector->gotoSimple('index', 'index', 'default');
    }

    public function lerAction()
    {
        $parametros = $this->getAllParams();

        if (isset($parametros['id'])) {
            $news = new Administrador_Model_Content();
            $results = $news->fetchRow($news->select()->from('content', array('id', 'title', 'text'))->where("id='" . $parametros['id'] . "'"));
            $this->view->results = $results;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione uma noticia para ler!'));
            return $this->_helper->redirector->gotoSimple('index', 'index', 'default');
        }
    }


}




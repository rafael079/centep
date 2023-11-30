<?php

class Cursos_SobreController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $parametros = $this->getAllParams();

        if (isset($parametros['curso'])) {
            $model = new Cursos_Model_Courses();
            
            $more = $model->fetchAll($model->select()->from(array('A' => 'courses'), array('id', 'name', 'description', 'value', 'workload')));
            $this->view->more = $more;
            
            $results = $model->fetchRow($model->select()->from(array('A' => 'courses'), array('id', 'name', 'description', 'value', 'workload'))->where("id='" . $parametros['curso'] . "'"));
            $this->view->results = $results;
        } else {
            $this->_helper->flashMessenger->addMessage(array('error' => 'Selecione um curso para mais detalhes!'));
            return $this->_helper->redirector->gotoSimple('index', 'index', 'cursos');
        }
    }

}


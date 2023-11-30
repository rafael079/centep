<?php

class Cursos_IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $model = new Cursos_Model_Courses();
        $results = $model->fetchAll($model->select()->from(array('A' => 'courses'), array('id', 'name', 'description', 'value', 'workload')));
        $this->view->results = $results;
    }

}


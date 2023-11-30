<?php

class Default_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $news = new Administrador_Model_Content();
        $results = $news->fetchAll($news->select()->from('content', array('id', 'title'))->order('date DESC'));
        $this->view->results = $results;
    }


}


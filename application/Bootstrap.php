<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initView() {
        $viewHelper = new Zend_View();
        $viewHelper->doctype('HTML5');
        $viewHelper->headMeta()->setCharset('UTF-8');
        $viewHelper->headTitle('CENTEP')->setSeparator(' - ');
    }

}


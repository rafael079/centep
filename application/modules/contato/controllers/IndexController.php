<?php

class Contato_IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $form = new Contato_Form_Contact();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($form->isValid($data)) {
                $configs = new Zend_Config_Xml(APPLICATION_PATH . '/configs/settings.xml', 'web');
                $mail = new Zend_Mail();
                $mail->setBodyText($form->message->getValue());
                $mail->setFrom($form->mail->getValue(), $form->name->getValue());
                $mail->addTo($configs->email, 'Atendimento Centep');
                $mail->setSubject('Contato pelo Site - ' . $form->subject->getValue());
                $send = $mail->send();
                if($send) {
                    $this->_helper->flashMessenger->addMessage(array('sucess' => 'Mensagem enviada com sucesso!'));
                    return $this->_helper->redirector->gotoSimple('index', 'index', 'contato');
                } else {
                    $this->_helper->flashMessenger->addMessage(array('error' => 'Desculpe! Ocorreu um erro ao tentar enviar a mensagem. Tente novamente.'));
                    return $this->_helper->redirector->gotoSimple('index', 'index', 'contato');
                }
            }
        }
        $this->view->form = $form;
    }

}


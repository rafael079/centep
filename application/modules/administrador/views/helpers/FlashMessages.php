<?php

class Administrador_View_Helper_FlashMessages extends Zend_View_Helper_Abstract {

    public function flashMessages() {
        $messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
        $output = '';

        if (!empty($messages)) {
            $output .= '';
            foreach ($messages as $message) {
                $output .= '<div class="alert '. key($message) . '"><span class="icon"></span>' . current($message) . '</div>';
            }
            $output .= '';
        }

        return $output;
    }

}


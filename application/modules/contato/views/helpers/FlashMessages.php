<?php

class Contato_View_Helper_FlashMessages extends Zend_View_Helper_Abstract {

    public function flashMessages() {
        $messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
        $output = '';

        if (!empty($messages)) {
            $output .= '';
            foreach ($messages as $message) {
                $output .= '<div class="alert alert-' . key($message) . '" style="display:none;">
									<button type="button" class="close" data-dismiss="alert">&times;</button>
									<i class="icon-ok"></i> ' . current($message) . '
								</div>';
            }
            $output .= '';
        }

        return $output;
    }

}


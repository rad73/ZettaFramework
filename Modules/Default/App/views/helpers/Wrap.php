<?php

class Zetta_View_Helper_Wrap extends Zend_View_Helper_Abstract {

	protected $_request;

	/**
	 * Обрамляем текуший текст шаблоном
	 *
	 * @param string $text
	 * @return string
	 */
    public function wrap($name, $function = false, $placeholderName = 'body', $model = null, $params = array()) {
		
    	if ('content_item' == $placeholderName || 'content' == $placeholderName) {
    		$this->view->placeholder($placeholderName)->captureStart();
    		$function();
    		$this->view->placeholder($placeholderName)->captureEnd();
    		return $this->view->renderAdmin($name, $placeholderName, $model);
    	}
    	
    	$return = $resultEcho = false;
    	 
    	if (is_callable($function)) {
    		$this->view->placeholder($placeholderName)->captureStart();
    		$return = $function();
    		$this->view->placeholder($placeholderName)->captureEnd();
    		$resultEcho = $this->view->placeholder($placeholderName)->toString();
    		Zend_View_Helper_Placeholder_Registry::getRegistry()->deleteContainer($placeholderName);
    	}
    	
        $this->view->$placeholderName = $return ? $return : $resultEcho;
		$this->view->params = $params;
		$return = $this->view->render($name);
		
		unset($this->view->$placeholderName);

		return $return;
    }


}
<?php

class Modules_Default_ErrorController extends Zend_Controller_Action {

	public function init() {

		$this->getFrontController()->unregisterPlugin('Modules_Admin_Plugin_Panel');
		
		$this->_response->clearBody();
		$this->view->getHelper('HeadLink')->exchangeArray(array());
		$this->view->getHelper('HeadScript')->exchangeArray(array());
		
		$this->getResponse()
			->clearHeaders()
			->setHttpResponseCode(404);

		$this->_helper->layout->setLayout('error');

			
		$this->_helper->getHelper('AjaxContext')
        	->addActionContext('error', 'html')
            ->initContext();
            

	}

	/**
	 * 404 ошибка - не найдена страница
	 *
	 */
	public function error404Action() {
		Zend_Registry::get('Logger')->log('Error 404: Page not found: ' . $this->view->currentUrl(), Zend_Log::WARN);
	}

	/**
	 * Любая другая ошибка
	 *
	 */
    public function errorAction() {

    	$errors = $this->_getParam('error_handler');
    	$this->view->error = $errors->exception;
    	
    	Zend_Registry::get('Logger')->log($errors->exception->getMessage(), Zend_Log::ERR, array(
    		'file'	=> $errors->exception->getFile(),
    		'line'	=> $errors->exception->getLine()
    	));
    	
    }

}

class ErrorController extends Modules_Default_ErrorController {}
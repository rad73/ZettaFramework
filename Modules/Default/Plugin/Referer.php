<?php

/**
 * Плагин для определения прошлой страницы альтернатива $_SERVER['HTTP_REFERER']
 * 
 * @example 
 * 
 * Zend_Registry::get('http_referer');
 * 
 */
class Modules_Default_Plugin_Referer extends Zend_Controller_Plugin_Abstract {

	protected $_session;
	
	public function routeStartup(Zend_Controller_Request_Abstract $request) {
		
		$this->_session = new Zend_Session_Namespace('Default');

		if ($this->_session->http_referer) {
			Zend_Registry::set('http_referer', $this->_session->http_referer);
		}

	}
	
	public function dispatchLoopShutdown() {
		
		if (
			(stristr($this->getResponse()->getBody(), '</html>') || $this->getRequest()->getParam('format') == 'html')
			&& $this->getResponse()->getHttpResponseCode() == 200
			&& 'mvc' != Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName()
		) {
			$this->_session->http_referer = $this->getRequest()->getRequestUri();
		}

	}

}
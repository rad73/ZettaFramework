<?php

/**
 * Плагин для размещения иконки в панели виджетов
 * 
 */
class Modules_Seo_Plugin_Widget extends Zend_Controller_Plugin_Abstract {

	protected $_view = null;
	
	public function __construct() {
		$this->_view = Zend_Registry::get('view');
	}
	
	public function routeStartup(Zend_Controller_Request_Abstract $request) {
		
		$this->_view->renderWidget(MODULES_PATH . DS . 'Seo/App/views', 'admin/widget.phtml');

    }

}
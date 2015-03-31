<?php

/**
 * Настраиваем autoloader
 *
 */
class Zetta_Bootstrap_Resource_Autoloader extends Zend_Application_Resource_ResourceAbstract {

	protected $_autoloader;
	
	public function init() {
		
		$this->_autoloader = Zend_Loader_Autoloader::getInstance();
		$this->_autoloader->setFallbackAutoloader(true);

	}

}
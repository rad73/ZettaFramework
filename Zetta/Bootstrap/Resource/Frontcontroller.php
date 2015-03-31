<?php

/**
 * Расширяем стандартный Frontcontroller
 *
 */
class Zetta_Bootstrap_Resource_Frontcontroller extends Zend_Application_Resource_Frontcontroller {

	protected $_frontController;
	
	public function init() {

		$this->_frontController = $this->getFrontController();
		$this->_setRequest();
		$this->_frontController = parent::init();
		
		return $this->_frontController;

	}

	

	/**
	 * Устанавливаем свой Request
	 */
	protected function _setRequest() {
		
		$options = $this->getOptions();
		if (array_key_exists('request', $options)) {
			
			$this->_frontController->setRequest(new $options['request']);
		}
		
		return $this;

	}

}
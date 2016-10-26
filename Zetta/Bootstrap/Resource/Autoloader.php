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

		$this->_pushPhpNSAutoloader();


	}

	/**
	 * Делаем возможным загружать классы с нативными PHP namespases
	 * @return self
	 */
	protected function _pushPhpNSAutoloader() {

		$this->_autoloader->pushAutoloader(function($className) {
			if (stristr($className, '\\') !== false) {
	            Zend_Loader_Autoloader::autoload(str_replace('\\', '_', $className));
			}
        });

		return $this;

	}

}

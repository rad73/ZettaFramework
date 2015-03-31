<?php

/**
 * Расширяем стандартный Cachemanager
 *
 */
class Zetta_Bootstrap_Resource_Cachemanager extends Zend_Application_Resource_Cachemanager {

	protected $_manager;
	
	public function init() {

		$this->_manager = parent::init();
		$this->_saveInRegistry();

		return $this->_manager;

	}

	/**
	 * Сохраняем объект кэша в реестре
	 * Теперь к нему можно обратиться Zend_Registry::get('cache')
	 */
	protected function _saveInRegistry() {
		Zend_Registry::set('cache', $this->_manager->getCache('default'));
	}

}
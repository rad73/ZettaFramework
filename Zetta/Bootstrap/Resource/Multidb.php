<?php

/**
 * Расширяем стандартный Multidb
 *
 */
class Zetta_Bootstrap_Resource_Multidb extends Zend_Application_Resource_Multidb {

	public function init() {

		parent::init();

		$this
			->_saveInRegistry()
			->_saveConfigRegistry()
			->_registerSqliteFunctions();

		return $this;

	}

	/**
	 * Сохраняем объект бд в реестре
	 * Теперь к нему можно обратиться Zend_Registry::get('db')
	 */
	protected function _saveInRegistry() {

		Zend_Registry::set('db', $this->getDefaultDb());
		Zend_Registry::set('dbs', $this->_dbs);

		return $this;

	}

	/**
	 * Сохраняем конфиг БД в реестре
	 * Теперь к нему можно обратиться Zend_Registry::get('db')
	 */
	protected function _saveConfigRegistry() {

		Zend_Registry::get('config')->db = (object)$this->getOptions();
		Zend_Registry::get('config')->db->staticSalt = $this->getDefaultDb()->getConfig()['staticSalt'];
		
		return $this;

	}

	protected function _registerSqliteFunctions() {

		foreach ($this->_dbs as $db) {

			if ($db instanceof Zend_Db_Adapter_Pdo_Sqlite) {
				$db->getConnection()->sqliteCreateFunction('md5', 'md5', 1);
			}

		}

	}
}
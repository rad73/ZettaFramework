<?php

/**
 * Расширяем стандартный Multidb
 *
 */
class Zetta_Bootstrap_Resource_Multidb extends Zend_Application_Resource_Multidb {

	/**
	 * БД по умолчанию
	 * @var Zend_Db_Adapter
	 */
	protected $_db;

	public function init() {

		parent::init();

		foreach($this->_dbs as $name=>$db) {

			$this->_db = $db;
			$this
				->_saveInRegistry($name)
				->_saveConfigRegistry($name)
				->_registerSqliteFunctions();

		}

		return $this;

	}

	/**
	 * Сохраняем объект бд в реестре
	 * Теперь к нему можно обратиться Zend_Registry::get('db')
	 */
	protected function _saveInRegistry($suffix) {

		$name = 'db' . ($this->isDefault($this->_db) ? '' : '_' . $suffix);
		Zend_Registry::set($name, $this->_db);

		return $this;

	}

	/**
	 * Сохраняем конфиг БД в реестре
	 * Теперь к нему можно обратиться Zend_Registry::get('db')
	 */
	protected function _saveConfigRegistry($suffix) {

		$name = 'DB' . ($this->isDefault($this->_db) ? '' : '_' . $suffix);
		Zend_Registry::get('config')->$name = (object)$this->getOptions();
		return $this;

	}

	protected function _registerSqliteFunctions() {

		if ($this->_db instanceof Zend_Db_Adapter_Pdo_Sqlite) {
			$this->_db->getConnection()->sqliteCreateFunction('md5', 'md5', 1);
		}

	}
}
<?php

abstract class Dbmigrations_Framework_Abstract implements Dbmigrations_Framework_Adapter_Interface {

	protected $_adapter;
	protected $_comment;
	protected $_db;

	public function __construct() {
		$this->_setDb();
	}

	/**
	 * Подключаем адаптер миграций
	 *
	 * @return Dbmigrations_Abstract
	 */
	protected function _setAdapter() {

		$db = $this->_db;

		switch (true) {

			case $db instanceof Zend_Db_Adapter_Mysqli:
			case $db instanceof Zend_Db_Adapter_Pdo_Mysql:
					$this->_adapter = new Dbmigrations_Framework_Adapter_Mysql($db);
				break;

			case $db instanceof Zend_Db_Adapter_Pdo_Sqlite:
					$this->_adapter = new Dbmigrations_Framework_Adapter_Sqlite($db);
				break;

		}

		return $this;

	}

	/**
	 * Сохраняем ссылку на БД
	 *
	 * @return Dbmigrations_Abstract
	 *
	 */
	protected function _setDb(Zend_Db_Adapter_Abstract $db = null) {

		$this->_db = $db ? $db : Zend_Registry::get('db');
		$this->_setAdapter();

		return $this;
	}

	public function getComment() {
		return $this->_comment;
	}

	public function createTable($name, $columns) {
		$this->_adapter->createTable($name, $columns);
	}
	public function renameTable($tableName, $newName) {
		$this->_adapter->renameTable($tableName, $newName);
	}
	public function dropTable($name) {
		$this->_adapter->dropTable($name);
	}

	public function createKey($table, $columnName, $keyName, $options = array('uniq'=>false, 'primary'=>false)) {
		$this->_adapter->createKey($table, $columnName, $keyName, $options);
	}
	public function	dropKey($table, $keyName) {
		$this->_adapter->dropKey($table, $keyName);
	}

	public function createForeignKey($table, $columnName, $keyName, $options = array('table'=>null, 'field'=>null, 'ondelete'=>null, 'onupdate'=>null)) {
		$this->_adapter->createForeignKey($table, $columnName, $keyName, $options);
	}
	public function dropForeignKey($table, $keyName) {
		$this->_adapter->dropForeignKey($table, $keyName);
	}

	public function	addColumn($table, $name, $options = array()) {
		$this->_adapter->addColumn($table, $name, $options);
	}

	public function	dropColumn($table, $name) {
		$this->_adapter->dropColumn($table, $name);
	}

	public function	alterColumn($table, $name, $newName, $options = array()) {
		$this->_adapter->alterColumn($table, $name, $newName, $options);
	}

	abstract public function up($params = false);
	abstract public function down($params = false);

}

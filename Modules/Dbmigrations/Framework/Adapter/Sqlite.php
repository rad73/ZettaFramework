<?php

class Dbmigrations_Framework_Adapter_Sqlite implements Dbmigrations_Framework_Adapter_Interface {

	protected $_db;

	public function __construct($db) {
		$this->_db = $db;
	}

	public function createTable($tableName, $columns) {
		
		/* ищем поле для автоинкремента */
		$columnPrimary = $columnToDelete = false;
		foreach ($columns as $name => $options) {
			if (array_key_exists('auto_increment', $options) && $options['auto_increment'] == 1) {
				$columnPrimary = $name;
				unset($columns[$name]);
			}
		}
		
		$columnToDelete = false;
		if (!$columnPrimary) {	// колонки с первичным ключём нету значит создаём с временной
			$columnToDelete = 'uniq_id';
		}
		
		$query = 'CREATE TABLE ' 
			. $this->_db->quoteIdentifier($tableName) 
			. ($columnToDelete ? (' (' . $this->_db->quoteIdentifier($columnToDelete) .' INTEGER PRIMARY KEY AUTOINCREMENT ) ') : '')	// создаём поле, а потом его снесём
			. ($columnPrimary ? (' (' . $this->_db->quoteIdentifier($columnPrimary) .' INTEGER PRIMARY KEY AUTOINCREMENT) ') : '');
			
		$this->_db->query($query);
		
		
		/* Добавим теперь к свежесозданной табличе столбцы */
		foreach ($columns as $name => $options) {
			$this->addColumn($tableName, $name, $options);
		}

	}

	public function dropTable($name) {
		$this->_db->query('DROP TABLE ' . $this->_db->quoteIdentifier($name));
	}

	public function renameTable($tableName, $newName) {
		$this->_db->query('RENAME TABLE ' .  $this->_db->quoteIdentifier($tableName) . '  TO ' .  $this->_db->quoteIdentifier($newName));
		
	}

	public function createKey($table, $columnName, $keyName, $options = array(
			'uniq'	=> false,
			'primary'	=> false
	)) {

		return false;
		$query = 'ALTER TABLE ' 
			. $this->_db->quoteIdentifier($table) 
			. ' ADD ' 
				. (isset($options['uniq']) && true == $options['uniq'] ? 'UNIQUE' : '') . ' ' 
				. (isset($options['primary']) && true == $options['primary'] ? 'PRIMARY' : '') 
			. ' KEY ' . $this->_db->quoteIdentifier($keyName) 
			. ' (' . (is_array($columnName) ? implode(',', $columnName) : $columnName) . ')';

		$this->_db->query($query);

	}

	public function dropKey($table, $key) {

		$query = 'ALTER TABLE ' . $this->_db->quoteIdentifier($table) . ' DROP KEY ' . $this->_db->quoteIdentifier($key);
		$this->_db->query($query);

	}

	public function createForeignKey($table, $columnName, $keyName, $options = array(
			'table'	=> null,
			'field'	=> null,
			'ondelete'	=> 'SET NULL',
			'onupdate'	=> 'CASCADE'
	)) {

		return false;	// @todo sqllite не поддерживает alter table нужно делать через создание новой таблицы но это не надёжно

		$this->createKey($table, $columnName, $keyName);

		$query = 'ALTER TABLE ' 
			. $this->_db->quoteIdentifier($table)
			. ' ADD CONSTRAINT ' . $this->_db->quoteIdentifier($keyName) . ' FOREIGN KEY '
			. $this->_db->quoteIdentifier($keyName) . ' (' . $this->_db->quoteIdentifier($columnName) . ')'
			. ' REFERENCES ' . $this->_db->quoteIdentifier($options['table']) . ' (' . $this->_db->quoteIdentifier($options['field']) . ')'
			. (isset($options['ondelete']) ? (' ON DELETE ' .  $options['ondelete']) : '')
			. (isset($options['onupdate']) ? (' ON UPDATE ' .  $options['onupdate']) : '');

		$this->_db->query($query);
    
	}

	public function dropForeignKey($table, $keyName) {
		$this->_db->query('ALTER TABLE ' . $this->_db->quoteIdentifier($table) . ' DROP FOREIGN KEY ' . $this->_db->quoteIdentifier($keyName));
	}

	public function addColumn($table, $name, $options = array(
			'type'	=> null, 
			'length'	=> null, 
			'unsigned'	=> false, 
			'auto_increment'	=> false,
			'default'	=> '',
			'null'	=> false
	)) {

		$query = 'ALTER TABLE ' 
			. $this->_db->quoteIdentifier($table)  
			. ' ADD COLUMN ' . $this->_makeStringColumn($name, $options);

		$this->_db->query($query);
		
		if (isset($options['keys'])) {
			$this->_createKeys($table, $name, $options['keys']);
		}
		if (isset($options['references'])) {
			$this->_createReferences($table, $name, $options['references']);
		}
				
	}

	public function	dropColumn($table, $name) {
		// DROP COLUMN не поддерживается sqllite
		// $this->_db->query('ALTER TABLE ' . $this->_db->quoteIdentifier($table) . ' DROP COLUMN ' . $this->_db->quoteIdentifier($name));
	}

	public function	alterColumn($table, $name, $newName, $options = array(
			'type'	=> null, 
			'length'	=> null, 
			'unsigned'	=> false, 
			'auto_increment'	=> false,
			'default'	=> '',
			'null'	=> false,
	)) {
		
		$query = 'ALTER TABLE '
			. $this->_db->quoteIdentifier($table)  
			. ' CHANGE ' . $this->_db->quoteIdentifier($name) 
			. ' ' . $this->_makeStringColumn($newName, $options, true);

		$this->_db->query($query);

		if (isset($options['keys'])) {
			$this->_createKeys($table, $newName, $options['keys']);
		}
		if (isset($options['references'])) {
			$this->_createReferences($table, $newName, $options['references']);
		}

	}

	protected function _makeStringColumn($columnName, $options, $alter = false) {

		return implode(' ', array(
			$this->_db->quoteIdentifier($columnName), 
			$options['type'], 
			(isset($options['length']) ? '(' . $this->_db->quote($options['length'], 'INTEGER') . ')' : ''),  
			(isset($options['unsigned']) ? 'unsigned' : ''),  
			(isset($options['null']) ? 'NULL' : 'NOT NULL'),
			'DEFAULT ' . $this->_db->quote(array_key_exists('default', $options) ? $options['default'] : ''),
			(isset($options['auto_increment']) ? ''
			. ($alter ? 'DROP PRIMARY KEY,' : '') 
			. 'ADD PRIMARY KEY (' . $this->_db->quoteIdentifier($columnName) . ')' : ''),
		));

	}


	/**
	 * Создаём скопом все ключи для столбца таблицы
	 *
	 * @param string $table
	 * @param string $column
	 * @param array $keys
	 * @return Dbmigrations_Adapter_Mysql
	 */
	protected function _createKeys($table, $column, $keys) {

		if (is_array($keys)) {
			foreach ($keys as $key_name=>$data) {
				$this->createKey($table, $column, $key_name, $data);
			}
		}

		return $this;

	}

	/**
	 * Создаём скопом все ссылки (внешние ключи) для столбца таблицы
	 *
	 * @param string $table
	 * @param string $column
	 * @param array $references
	 * @return Dbmigrations_Adapter_Mysql
	 */
	protected function _createReferences($table, $column, $references) {

		if (is_array($references)) {
			foreach ($references as $key_name=>$data) {
				$this->createForeignKey($table, $column, $key_name, $data);
			}
		}

		return $this;

	}

}
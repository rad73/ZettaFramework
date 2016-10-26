<?php

class Dbmigrations_Framework_Adapter_Mysql implements Dbmigrations_Framework_Adapter_Interface {

	protected $_db;

	public function __construct($db) {
		$this->_db = $db;
	}

	public function createTable($tableName, $columns) {

		$columnToDelete = 'to_delete_' . substr(md5(rand()), 0, 5);
		$query = 'CREATE TABLE '
			. $this->_db->quoteIdentifier($tableName)
			. ' (' . $this->_db->quoteIdentifier($columnToDelete) .' INT ) ' 	// создаём поле, а потом его снесём
			. 'ENGINE=InnoDB CHARSET=utf8';

		$this->_db->query($query);

		/* Добавим теперь к свежесозданной табличе столбцы */
		foreach ($columns as $name => $options) {
			$this->addColumn($tableName, $name, $options);
		}

		/* Удаляем временный столбец */
		$this->dropColumn($tableName, $columnToDelete);

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
			'comment'	=> '',
			'default'	=> '',
			'null'	=> false,
			'after'	=> false,
	)) {

		$query = 'ALTER TABLE '
			. $this->_db->quoteIdentifier($table)
			. ' ADD COLUMN ' . $this->_makeStringColumn($name, $options)
			. (!empty($options['after']) ? ' AFTER ' . $this->_db->quoteIdentifier($options['after']) : '');

		$this->_db->query($query);

		if (isset($options['keys'])) {
			$this->_createKeys($table, $name, $options['keys']);
		}
		if (isset($options['references'])) {
			$this->_createReferences($table, $name, $options['references']);
		}

	}

	public function	dropColumn($table, $name) {
		$this->_db->query('ALTER TABLE ' . $this->_db->quoteIdentifier($table) . ' DROP COLUMN ' . $this->_db->quoteIdentifier($name));
	}

	public function	alterColumn($table, $name, $newName, $options = array(
			'type'	=> null,
			'length'	=> null,
			'unsigned'	=> false,
			'auto_increment'	=> false,
			'comment'	=> '',
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
			(isset($options['length']) ? '(' . $options['length'] . ')' : ''),
			(isset($options['unsigned']) ? 'unsigned' : ''),
			(isset($options['null']) ? 'NULL' : 'NOT NULL'),
			(isset($options['default']) ? 'DEFAULT ' . $this->_db->quote($options['default']) : ''),
			(isset($options['comment']) ? ' COMMENT ' . $this->_db->quote($options['comment']) : ''),
			(isset($options['auto_increment']) ? 'AUTO_INCREMENT, '
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

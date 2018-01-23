<?php

class Zetta_Db_Table extends Zend_Db_Table {

	/**
	 * Все данные из таблицы
	 *
	 * @var array
	 */
	private static $_fullData = null;

	/**
	 * Имя соединения
	 *
	 * @var string
	 */
	protected $_connectionName;


	public function init() {

		if (defined('static::TABLE_NAME')) {
			$this->_name = static::TABLE_NAME;
		}

		if (defined('static::CONNECTION') || $this->_connectionName) {
			$this->setConnection($this->_connectionName ?: static::CONNECTION);
		}

		parent::init();

	}
	
	/**
	 * Установка источника БД 
	 *
	 * @return string
	 */
	public function setConnection($name) {
		
		if (
			Zend_Registry::isRegistered("dbs")
			&& isset(Zend_Registry::get("dbs")[$name])
		) {
			$this->_connectionName = $name;
	    	return $this->_setAdapter(Zend_Registry::get("dbs")[$name]);
		}
		else {
			throw new \Zend_Db_Exception('DbConnection name ' . $name . ' not found');
		}
		
	}

	/**
	 * Выборка всех данных из таблицы и сохранение их в self::$_fullData
	 *
	 * @return Zend_Db_Table_Rowset
	 */
	public function fetchFull() {

		$tableName = $this->info('name');

		if (false == isset(self::$_fullData[$tableName])) {
			self::$_fullData[$tableName] = $this->fetchAll();
		}

		return self::$_fullData[$tableName];

	}

	/**
	 * Получаем данные в виде ассоциацивного массива вида ключ => значение
	 *
	 * @param string $keyId		ID ключа будущего массива
	 * @param string $valueId	ID значения будущего массива
	 * @param Zend_Db_Select $select
	 * @return array
	 */
	public function fetchAssoc($keyId, $valueId, Zend_Db_Select $select = null) {

		$data = array();
		$select = $select ? $select : $this->select();
		$stmt = $this->getAdapter()->query($select->from($this->info('name'), array($keyId, $valueId)));

        while ($row = $stmt->fetch(Zend_Db::FETCH_NUM)) {
            $data[$row[0]] = $row[1];
        }

		$stmt->closeCursor();

        return $data;

	}

	/**
	 * Уничтожаем кеш данных
	 *
	 * @return self
	 */
	protected function _cleanFullData() {

		$tableName = $this->info('name');

		if (false == isset(self::$_fullData[$tableName])) {
			unset(self::$_fullData[$tableName]);
		}

		return $this;

	}

	public function delete($where) {
		$return = parent::delete($where);
		$this->_cleanFullData();
		return $return;
	}

	public function update(array $data, $where) {
		$return = parent::update($data, $where);
		$this->_cleanFullData();
		return $return;
	}

	public function insert(array $data) {
		$return = parent::insert($data);
		$this->_cleanFullData();
		return $return;
	}
	
	public function getDatabaseName() {
		return $this->getAdapter()->getConfig()['dbname'];
	}

}

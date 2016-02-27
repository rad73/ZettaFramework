<?php

class Zetta_Db_Table extends Zend_Db_Table {

	/**
	 * Все данные из таблицы
	 *
	 * @var array
	 */
	private static $_fullData = null;


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

}

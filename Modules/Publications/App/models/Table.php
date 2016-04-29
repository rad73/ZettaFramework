<?php

class Modules_Publications_Model_Table extends Zend_Db_Table  {

	const PREFIX_TABLE = 'publications__';

	protected $_cleanName;

	protected static $linkedData = array();
	protected static $linkedFieldsTableData = array();
	protected static $setupedTables = array();

	protected $_tableProfile;

	protected $_route_id;

	/**
	 * Устанавливаем ID маршрута в котором будем искать публикации
	 *
	 * @param int $route_id
	 */
	public function setRouteId($route_id) {
		$this->_route_id = intval($route_id);
	}

	/**
	 * Все таблицы для хранения данных имеют префикс
	 *
	 */
	protected function _setup() {

		parent::_setup();

		$this->_cleanName = $this->_name;
		$this->_name = self::PREFIX_TABLE . $this->_name;

		if (false == array_key_exists($this->_cleanName, self::$setupedTables)) {

			$modelFields = new Modules_Publications_Model_Fields();

			$this->_tableProfile = $modelFields->fetchAllByTableName($this->_cleanName);

			foreach ($this->_tableProfile as $row) {

				if ($row->list_values) {

					if (false == array_key_exists($row->list_values, self::$linkedData)) {
						if ('routes' == $row->list_values) {
							self::$linkedData[$row->list_values] = Modules_Router_Model_Router::getInstance()->getRoutesTreeHash();
						}
						else {
							$model = new self($row->list_values);
							self::$linkedData[$row->list_values] = $model->fetchAll()->toArray();
						}
					}

					self::$linkedFieldsTableData[$this->_cleanName . '_' . $row->name] = self::$linkedData[$row->list_values];

				}

			}

			self::$setupedTables[$this->_cleanName] = $this->_tableProfile;

		}
		else {
			$this->_tableProfile = self::$setupedTables[$this->_cleanName];
		}

	}

	/**
	 * Выбираем публикации в виде ассоциативного массива
	 *
	 * @param string $keyName
	 * @param string $valueName
	 * @return array
	 */
	public function getAssocArray($keyName, $valueName) {

		$data = $this->fetchAll($this->select()
			->where('active = 1')
			->order('sort'));

		$return = array();

		foreach ($data as $row) {
			$return[$row[$keyName]] = $row[$valueName];
		}

		return $return;

	}

	/**
     * Inserts a new row.
     *
     * @param  array  $data  Column-value pairs.
     * @return mixed         The primary key of the row inserted.
     */
    public function insert(array $data) {

		$rowMaxSort = $this->fetchRow($this->select()->from($this->info('name'), array(new Zend_Db_Expr("MAX(sort) AS sort"))));
		$data['sort'] = ($rowMaxSort) ? (int)$rowMaxSort->sort + 1 : 1;

    	return parent::insert($data);

    }

	/**
	 * Переписываем стандартный _fetch с учётом выборки связанных данных
	 *
	 * @param Zend_Db_Table_Select $select
	 */
	protected function _fetch(Zend_Db_Table_Select $select) {

		if ($this->_route_id) {
			$select = $select->where('route_id = ?', $this->_route_id);
		}

		$rows = parent::_fetch($select);

		foreach ($rows as &$row) {

			foreach ($row as $fieldName => $field) {

				if (array_key_exists($this->_cleanName . '_' . $fieldName, self::$linkedFieldsTableData)) {

					if ($this->_isSerialized($row[$fieldName])) {
						$row[$fieldName] = $this->_unserialize($row[$fieldName]);
						if (sizeof($row[$fieldName])) {
							$field = implode(',', $row[$fieldName]);
						}
					}

					$data = explode(',', $field);

					foreach ($data as $val) {

						$val = chop($val);

						foreach (self::$linkedFieldsTableData[$this->_cleanName . '_' . $fieldName] as $linked_row) {

							if (is_array($linked_row) && $val == $linked_row['publication_id']) {

								if (false == array_key_exists($fieldName . '_linked', $row) || false == is_array($row[$fieldName . '_linked'])) {
									$row[$fieldName . '_linked'] = array();
								}
								array_push($row[$fieldName . '_linked'], $linked_row);

								break;

							}

						}

					}

				}

			}

			$row['rubric_id'] =
				$row['pub_rubric_id'] = $this->_tableProfile[0]->rubric_id;

		}

		return $rows;

	}

	public function getTableProfile() {
		return $this->_tableProfile;
	}

	/**
	 * Получаем публикации c рубрикой
	 *
	 * @return Zend_Db_Rowset
	 */
	public function getWithRubrics($pageNumber = 1, $onPage = 25) {

		$select = $this->select()
			->where('route_id = ?', $this->_route_id)
			->order('sort')
			->order('publication_id')
		;

		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
		$paginator->setCurrentPageNumber($pageNumber);
		$paginator->setItemCountPerPage($onPage);

		return $paginator;

	}

	/**
	 * Получаем публикации без рубрики
	 *
	 * @return Zend_Db_Rowset
	 */
	public function getWithoutRubrics($pageNumber = 1, $onPage = 25) {

		$select = $this->select()
			->where('route_id IS NULL')
			->order('sort')
			->order('publication_id')
		;

		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
		$paginator->setCurrentPageNumber($pageNumber);
		$paginator->setItemCountPerPage($onPage);

		return $paginator;

	}

	public function sort($current, $next = false, $prev = false) {

		if (false == $next && false == $prev) return;

		// узнаем новое значение сортировки для элемента
		if ($prev) {
			$sort = 1 + $this->getAdapter()->fetchOne($this->select()->from($this->info('name'), array('sort'))->where('publication_id = ? ', $prev));
		}
		else if ($next) {
			$sort = $this->getAdapter()->fetchOne($this->select()->from($this->info('name'), array('sort'))->where('publication_id = ? ', $next));
		}

		// сортируем элемент который перетащили
		$this->update(array(
			'sort' => $sort
		), $this->getAdapter()->quoteInto('publication_id = ?', $current));

		// сортируем все элементы которые старше по сотритовке чем наш
		$this->update(array(
			'sort' => new Zend_Db_Expr('sort + 1')
		), array(
				$this->getAdapter()->quoteInto('sort >= ?', $sort),
				$this->getAdapter()->quoteInto('publication_id != ?', $current)
			)
		);

	}

	protected function _isSerialized($data) {

		if (!is_string( $data)) {
			return false;
		}

		return System_String::Substr($data, 0, 1) == '÷' && System_String::Substr($data, -1, 1) == '÷';

	}

	protected function _unserialize($string) {

		if (!is_string($string)) {
			return false;
		}

		$string = System_String::Substr($string, 1, -1);
		return explode('÷', $string);

	}

}
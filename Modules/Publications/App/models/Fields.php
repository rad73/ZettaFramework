<?php

class Modules_Publications_Model_Fields extends Zetta_Db_Table  {

	protected $_name = 'publications_fields';

	protected $_convertTypes = array(
		'text'	=> array('varchar', 255),
		'textarea'	=> array('longtext', null),
		'select'	=> array('varchar', 255),
		'radio'	=> array('varchar', 255),
		'checkbox'	=> array('varchar', 255),
		'password'	=> array('varchar', 255),
		'file'	=> array('text', null),
		'html'	=> array('longtext', null),
		'date'	=> array('date', null),
		'datetime'	=> array('datetime', null),
		'file_dialog'	=> array('varchar', 255),
		'route'	=> array('int', 10),
	);

	private static $_fullData = null;


	/**
	 * insert с учётом добавления нового поля в таблицу публикаций
	 *
	 * @param array $data
	 */
	public function insert(array $data) {

		$rowID = parent::insert($data);

		if ($rowID && 'captcha' != $data['type']) {

			/* создаём поле в таблице хранения данных */

			$modelList = new Modules_Publications_Model_List();
			$tableInfo = $modelList->fetchRow($modelList->select()->where('rubric_id = ?', $data['rubric_id']));

			$tableName = $tableInfo->table_name;
			$filedName = $data['name'];
			$params = array(
				'type'	=> $this->_convertTypes[$data['type']][0],
				'length'	=> $this->_convertTypes[$data['type']][1],
				'null'	=> true
			);

			$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
			$_migrationManager->upTo('Modules_Publications_Migrations_CreatePublicationAbstractFieled', array($tableName, $filedName, $params), false);

		}

	}

	/**
	 * delete с учётом удаления поля из таблицы публикаций
	 *
	 * @param  array|string $where SQL WHERE clause(s).
     * @return int          The number of rows deleted.
	 */
	public function delete($where) {

		$resultSet = $this->fetchAll($where);

		if (sizeof($resultSet)) {

			$modelList = new Modules_Publications_Model_List();

			foreach ($resultSet as $row) {

				$tableInfo = $modelList->fetchRow($modelList->select()->where('rubric_id = ?', $row->rubric_id));

				$tableName = $tableInfo->table_name;
				$filedName = $row->name;

				$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
				$_migrationManager->downTo('Modules_Publications_Migrations_CreatePublicationAbstractFieled', array($tableName, $filedName));
			}
		}

		return parent::delete($where);

	}

	/**
	 * Получаем поля конкретного типа публикаций
	 *
	 * @param int $rubric_id
	 * @return Zend_Db_Rowset
	 */
	public function getFieldsByRubric($rubric_id) {

		return $this->fetchAll(
			$this->select()
				->where('rubric_id = ?', $rubric_id)
				->order('sort')
				->order('field_id')
		);

	}

	/**
	 * Ищем поле
	 *
	 * @param string $field_name
	 * @param int $rubric_id
	 * @return Zend_Db_Rowset
	 */
	public function findFiled($field_name, $rubric_id) {

		return $this->fetchAll($this->select()
			->where('name = ?', $field_name)
			->where('rubric_id = ?', $rubric_id)
		);

	}

	/**
	 * Получаем информацию о полях в определённой таблице
	 *
	 * @param string $tableName
	 * @return array
	 */
	public function fetchAllByTableName($tableName) {

		$returnArray = array();

		foreach($this->fetchFull() as $i=>$row) {

			if ($row->table_name == $tableName) {
				array_push($returnArray, $row);
			}

		}

		return $returnArray;

	}

	public function fetchFull() {

		if (null === self::$_fullData) {

			$modelList = new Modules_Publications_Model_List();

			self::$_fullData = $this->fetchAll($this->select()
				->setIntegrityCheck(false)
				->from(array('f' => $this->info('name')), '*')
				->join(array('r' => $modelList->info('name')), 'f.rubric_id = r.rubric_id', array('table_name'))
			);

		}

		return self::$_fullData;

	}

}

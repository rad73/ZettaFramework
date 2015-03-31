<?php

class Modules_Publications_Model_List extends Zend_Db_Table  {

	protected $_name = 'publications_list';
	
	/**
	 * Получаем информацию о типе публикаций
	 *
	 * @param int|string $rubric_id
	 * @return Zend_Db_Row
	 */
	public function getRubricInfo($rubric_id) {
		
		if (is_numeric($rubric_id)) {
			return $this->fetchRow($this->select()->where('rubric_id = ?', $rubric_id));
		}
		else {
			return $this->fetchRow($this->select()->where('table_name = ?', $rubric_id));
		}

	}

	/**
	 * insert с учётом создания таблицы для хранения данных о публикации
	 *
	 * @param array $data
	 * @return int
	 */
	public function insert(array $data) {
		
		$rowID = parent::insert($data);
		
		if ($rowID) {
		
			/* создаём таблицу для хранения данных */
			$tableName = $data['table_name'];
			if (!System_Functions::tableExist($tableName)) {
			
				$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
				$_migrationManager->upTo('Modules_Publications_Migrations_CreatePublicationAbstractTable', $tableName, false);
				
				// Добавляем базовые поля
				$filedsModel = new Modules_Publications_Model_Fields();
				
				$filedsModel->insert(array(
					'rubric_id'		=> $rowID,
					'name'	=> 'name',
					'title'	=> 'Название',
					'type'	=> 'text',
					'validator'	=> '.*',
					'sort'	=> 1,
				));
				
				$filedsModel->insert(array(
					'rubric_id'		=> $rowID,
					'name'	=> 'active',
					'title'	=> 'активно',
					'type'	=> 'checkbox',
					'default'	=> '1',
					'sort'	=> 2,
				));
				
	
			}
				
		}
		
		return $rowID;
		
	}

	/**
	 * delete с учётом удаления таблицы с публикациями
	 *
	 * @param  array|string $where SQL WHERE clause(s).
     * @return int          The number of rows deleted.
	 */
	public function delete($where) {

		$resultSet = $this->fetchAll($where);
		
		if (sizeof($resultSet)) {
			foreach ($resultSet as $row) {
				$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
				$_migrationManager->downTo('Modules_Publications_Migrations_CreatePublicationAbstractTable', $row->table_name);
			}
		}
		
		return parent::delete($where);

	}

}
<?php

/**
 * Миграция по созданию таблицы для навигации
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Publications_Migrations_CreatePublicationListTable extends Dbmigrations_Framework_Abstract {

	protected $_comment = 'Создание таблицы списка рубрик публикаций';

	
	public function up($params = null) {
		
		$this->createTable('publications_list', array(
		
			'rubric_id'	=> array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'auto_increment'	=>	1,
			),
		
			'name'	=> array(
				'type'	=>	'varchar',
				'length'	=>	150,
				'comment'	=>	'Название рубрики',
			),
			
			'table_name' => array(
				'type'		=>	'varchar',
				'length'	=>	25,
				'comment'	=>	'Имя таблицы',
			),
			
		));

	}

	public function down($params = null) {
		$this->dropTable('publications_list');
	}
	
}
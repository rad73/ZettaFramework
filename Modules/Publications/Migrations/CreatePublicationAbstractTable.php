<?php

/**
 * Миграция по созданию таблицы для хранения публикаций
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Publications_Migrations_CreatePublicationAbstractTable extends Dbmigrations_Framework_Abstract {

	protected $_comment = 'Создание таблицы полей для хранения публикаций';

	
	public function up($table_name = false) {
		
		$this->createTable(Modules_Publications_Model_Table::PREFIX_TABLE . $table_name, array(
		
			'publication_id'	=> array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'auto_increment'	=>	1,
			),
			
			'route_id'	=> array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'null'		=> true,
			),
			
			'sort' => array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'null'		=> true,
			),
			
		));

	}

	public function down($table_name = false) {
		$this->dropTable(Modules_Publications_Model_Table::PREFIX_TABLE . $table_name);
	}
	
}
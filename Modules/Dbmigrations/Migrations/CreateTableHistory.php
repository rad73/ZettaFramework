<?php

class Modules_Dbmigrations_Migrations_CreateTableHistory extends Modules_Dbmigrations_Framework_Abstract {

	protected $_comment = 'Создание таблицы для логирования миграций у текущего разработчика';
	protected $_tableName = 'dbmigrations_history';

	public function up($params = null) {

		$this->createTable($this->_tableName, array(
			'id'	=> array(
				'type'	=>	'int',
				'unsigned'	=>	'1',
				'auto_increment'	=>	'1',
				'comment'	=>	'Уникальный номер миграции',
			),
			'date'	=>	array(
				'type'	=>	'datetime',
				'comment'	=>	'Дата когда была произведена миграция',
			),
			'class_name'	=> array(
				'type'	=>	'varchar',
				'length'	=>	'100',
				'comment'	=>	'Класс который породил миграцию',
				'keys'		=> 	array(
					'uniq_class_name'	=> array(
						'uniq'	=> true
					)
				),
			),
			'comment'	=> array(
				'type'	=>	'varchar',
				'length'	=>	'255',
				'null'	=>	1,
				'comment'	=>	'Коментарий к миграции',
			)
		));

	}

	public function down($params = null) {
		$this->dropTable($this->_tableName);
	}

}

<?php

/**
 * Миграция по созданию таблицы полей для публикаций
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Publications_Migrations_CreatePublicationFieldsTable extends Dbmigrations_Framework_Abstract {

	protected $_comment = 'Создание таблицы полей для публикаций';

	
	public function up($params = null) {
		
		$this->createTable('publications_fields', array(
		
			'field_id'	=> array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'auto_increment'	=>	1,
			),
		
			'rubric_id'	=> array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'comment'	=>	'ID рубрики с данными',
				'references'	=>	array(
					'rubric_id_fk'	=>	array(
						'table'	=>	'publications_list',
						'field'	=>	'rubric_id',
						'ondelete'	=>	'CASCADE',
						'onupdate'	=>	'CASCADE'
					)
				),
			),
			
			'name'	=> array(
				'type'	=>	'varchar',
				'length'	=>	50,
				'comment'	=>	'Название поля',
			),
			
			'title' => array(
				'type'		=>	'varchar',
				'length'	=>	100,
				'comment'	=>	'Заголовок поля',
			),
			
			'type' => array(
				'type'		=>	'varchar',
				'length'	=>	25,
				'comment'	=>	'Тип поля',
			),
			
			'validator' => array(
				'type'		=>	'varchar',
				'length'	=>	50,
				'comment'	=>	'Валидатор для поля',
				'null'		=> true,
			),
			
			'default' => array(
				'type'		=>	'varchar',
				'length'	=>	255,
				'comment'	=>	'Значение по умолчанию',
				'null'		=> true,
			),
			
			'errormsg' => array(
				'type'		=>	'varchar',
				'length'	=>	255,
				'comment'	=>	'Текст сообщения об ошибке',
				'null'		=> true,
			),
			
			
			'list_values' => array(
				'type'		=>	'varchar',
				'length'	=>	255,
				'comment'	=>	'Значения для списка',
				'null'		=> true,
			),
			
			'hidden_front' => array(
				'type'		=>	'char',
				'length'	=>	1,
				'comment'	=>	'Выводить поле только администратору',
				'null'		=> true,
			),
			
			'sort' => array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'null'		=> true,
			),
			
		));

	}

	public function down($params = null) {
		$this->dropTable('publications_fields');
	}
	
}
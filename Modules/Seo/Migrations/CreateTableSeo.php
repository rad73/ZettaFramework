<?php

/**
 * Миграция по созданию поля таблицы для seo данных
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Seo_Migrations_CreateTableSeo extends Modules_Dbmigrations_Framework_Abstract {

	protected $_comment = 'Создание таблицы для seo';

	
	public function up($params = false) {

		$this->createTable('seo', array(
		
			'seo_id'	=> array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'auto_increment'	=>	1,
			),
		
			'url'	=> array(
				'type'	=>	'varchar',
				'length'	=>	255,
				'comment'	=>	'URL адрес',
			),
			
			'title'	=> array(
				'type'	=>	'varchar',
				'length'	=>	100,
				'comment'	=>	'Заголовок',
				'null'	=> true,
			),
			
			'keywords'	=> array(
				'type'	=>	'varchar',
				'length'	=>	255,
				'comment'	=>	'Ключевые слова',
				'null'	=> true,
			),
			
			'description'	=> array(
				'type'	=>	'varchar',
				'length'	=>	255,
				'comment'	=>	'Описание',
				'null'	=> true,
			)
			
		));
		
	}

	public function down($params = false) {

		$this->dropTable('seo');

	}
	
}

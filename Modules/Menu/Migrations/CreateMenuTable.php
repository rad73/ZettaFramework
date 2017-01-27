<?php

/**
 * Миграция по созданию таблицы для меню
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Menu_Migrations_CreateMenuTable extends Modules_Dbmigrations_Framework_Abstract {

	protected $_comment = 'Создание таблицы меню';

	
	public function up($params = null) {
		
		$this->createTable('menu', array(
		
			'menu_id'	=> array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'auto_increment'	=>	1,
			),
		
			'name'	=> array(
				'type'	=>	'varchar',
				'length'	=>	255,
				'comment'	=>	'Название меню',
			),
			
			'type'	=> array(
				'type'	=>	'varchar',
				'length'	=>	10,
				'comment'	=>	'Тип меню (router или free)',
			),
			
			'parent_route_id'	=> array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'references'	=>	array(
					'menu__routes'	=> array(
						'table'	=> 'routes',
						'field'	=> 'route_id',
						'ondelete'	=> 'CASCADE',
						'onupdate'	=> 'CASCADE',
					)
				),
				'null'	=> true
			),
			
		));
		
	}

	public function down($params = null) {
		$this->dropTable('menu');
	}
	
}

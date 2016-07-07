<?php

/**
 * Миграция по добавлению главного меню
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Menu_Migrations_AddDefaultMenu extends Dbmigrations_Framework_Abstract {

	protected $_comment = 'Добавляем главное меню по умолчанию';

	
	public function up($params = null) {
		
		$model = new Modules_Menu_Model_Menu();
		
		$model->insert(array(
			'menu_id'	=> 1,
			'name'		=> 'Главное меню',
			'type'		=> 'router',
			'parent_route_id'	=> '1',
		));
		
	}

	public function down($params = null) {
		
	}
	
}
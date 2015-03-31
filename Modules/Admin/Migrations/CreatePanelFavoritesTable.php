<?php

/**
 * Миграция по созданию таблицы для избранных пунктов у пользователя
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Admin_Migrations_CreatePanelFavoritesTable extends Dbmigrations_Framework_Abstract {

	protected $_comment = 'Создание таблицы избранных пунктов у пользователя';

	
	public function up($params = null) {
		
		$this->createTable('admin_panel_favorites', array(
			
			'id'	=> array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'auto_increment'	=>	1,
			),
		
			'username'	=> array(
				'type'	=>	'varchar',
				'length'	=>	50,
				'comment'	=>	'Логин пользователя',
				/*
				'references'	=>	array(
					'favorites_username_fk_access_users'	=>	array(
						'table'	=>	'access_users',
						'field'	=>	'username',
						'ondelete'	=>	'CASCADE',
						'onupdate'	=>	'CASCADE'
					)
				),
				*/ // superadmin
			),
			
			'module'	=> array(
				'type'	=>	'varchar',
				'length'	=>	25,
				'comment'	=>	'Имя модуля',
			),
			
		));
		
	}

	public function down($params = null) {
		$this->dropTable('admin_panel_favorites');
	}
	
}
<?php

/**
 * Миграция по созданию таблицы для пользователей
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Access_Migrations_CreateUsersTable extends Dbmigrations_Framework_Abstract {

	protected $_comment = 'Создание таблицы пользователей';

	
	public function up($params = null) {
		
		$this->createTable('access_users', array(
			'username'	=> array(
				'type'	=>	'varchar',
				'length'	=>	50,
				'comment'	=>	'Логин пользователя',
				'keys'	=> array(
					'p_username'	=> array('primary'	=> true),
					'uniq_username'	=> array('uniq'	=> true),
				)
			),
			
			'password'	=> array(
				'type'	=>	'varchar',
				'length'	=>	32,
				'comment'	=>	'MD5 хэш пароля пользователя',
			),
			
			'salt'	=> array(
				'type'	=>	'varchar',
				'length'	=>	32,
				'comment'	=>	'Соль',
			),
			
			'role_name'	=> array(
				'type'	=>	'varchar',
				'null'	=>	true,
				'length'	=>	32,
				'comment'	=>	'ID роли к которой принадлежит пользователь',
				'references'	=>	array(
					'access_users__access_roles'	=>	array(
						'table'	=>	'access_roles',
						'field'	=>	'name',
						'ondelete'	=>	'CASCADE',
						'onupdate'	=>	'CASCADE'
					)
				),
			),

			'active'	=> array(
				'type'	=>	'tinyint',
				'length'	=>	1,
				'default'	=>	1,
				'comment'	=>	'Включён ли пользователь'
			),

			'email'	=> array(
				'type'	=>	'varchar',
				'length'	=>	255,
				'comment'	=>	'E-mail пользователя',
			),
			
			'name'	=> array(
				'type'	=>	'varchar',
				'length'	=>	255,
				'comment'	=>	'Имя пользователя',
			),
			
			'sername'	=> array(
				'type'	=>	'varchar',
				'length'	=>	255,
				'comment'	=>	'Фамилия пользователя',
			),

		));
		
	}

	public function down($params = null) {
		$this->dropTable('access_users');
	}
	
}
<?php

/**
 * Миграция по созданию таблицы правил
 *
 */
class Modules_Access_Migrations_CreateRulesTable extends Dbmigrations_Framework_Abstract {

	protected $_comment = 'Создание таблицы правил';


	public function up($params = false) {
		
		$this->createTable('access_rules', array(
			'rule_id'	=> array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'auto_increment'	=>	1,
			),
			'resource_name'	=> array(
				'type'	=>	'varchar',
				'length'	=>	255,
				'comment'	=>	'Идентификатор ресурса',
				'references'	=>	array(
					'access_rules__access_resources'	=>	array(
						'table'	=>	'access_resources',
						'field'	=>	'resource_name',
						'ondelete'	=>	'CASCADE',
						'onupdate'	=>	'CASCADE'
					)
				),
			),
			'role_name'	=> array(
				'type'	=>	'varchar',
				'length'	=>	32,
				'comment'	=>	'Идентификатор роли',
				'references'	=>	array(
					'access_rules__access_roles'	=>	array(
						'table'	=>	'access_roles',
						'field'	=>	'name',
						'ondelete'	=>	'CASCADE',
						'onupdate'	=>	'CASCADE'
					)
				),
			),
			'is_allowed'	=> array(
				'type'	=>	'tinyint',
				'length'	=> 1,
				'comment'	=>	'Есть доступ?',
			),
		));
		
	}

	public function down($params = false) {
		$this->dropTable('access_rules');
	}
	
}
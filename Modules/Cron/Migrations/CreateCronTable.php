<?php

class Modules_Cron_Migrations_CreateCronTable extends Dbmigrations_Framework_Abstract {

	protected $_comment = 'Создание таблицы планировщика';


	public function up($params = false) {
		
		$this->createTable('cron', array(
			'cron_id'	=> array(
				'type'		=>	'int',
				'unsigned'	=>	1,
				'auto_increment'	=>	1,
			),
			'minute'	=> array(
				'type'	=>	'varchar',
				'length'	=>	10,
				'comment'	=>	'Минута',
			),
			'hour'	=> array(
				'type'	=>	'varchar',
				'length'	=>	10,
				'comment'	=>	'Час',
			),
			'day'	=> array(
				'type'	=>	'varchar',
				'length'	=>	10,
				'comment'	=>	'День',
			),
			'month'	=> array(
				'type'	=>	'varchar',
				'length'	=>	10,
				'comment'	=>	'Месяц',
			),
			'week_day'	=> array(
				'type'	=>	'varchar',
				'length'	=>	10,
				'comment'	=>	'День недели',
			),
			'task'	=> array(
				'type'	=>	'text',
			),
			'last_run_start'	=> array(
				'type'	=>	'datetime',
				'comment'	=>	'Когда был последний запуск (начало)',
			),
			'last_run_finish'	=> array(
				'type'	=>	'datetime',
				'comment'	=>	'Когда был последний запуск (конец)',
			),
			'in_progress'	=> array(
				'type'	=>	'char',
				'length'	=>	'1',
				'comment'	=>	'Выполнется сейчас',
				'null'	=> 1
			),
			'active'	=> array(
				'type'	=>	'char',
				'length'	=>	'1',
				'comment'	=>	'Активна ли задача',
				'null'	=> 1
			),
		));
		
	}

	public function down($params = false) {
		$this->dropTable('cron');
	}
	
}
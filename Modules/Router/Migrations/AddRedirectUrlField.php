<?php

/**
 * Миграция по добавлению поля redirect_url
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Router_Migrations_AddRedirectUrlField extends Modules_Dbmigrations_Framework_Abstract {

	protected $_comment = 'Добавляем поле redirect_url';

	/**
	 * Модель маршрутизатора
	 *
	 * @var Modules_Router_Model_Router
	 */
	protected $_model;


	public function __construct() {

		parent::__construct();
		$this->_model = new Modules_Router_Model_Router();

	}

	public function up($params = null) {

		$this->addColumn($this->_model->info('name'), 'redirect_url', array(
			'type'	=>	'varchar',
			'length'	=>	255,
			'comment'	=>	'URL для переадресации',
			'null'	=>	true,
		));

	}

	public function down($params = null) {
		$this->dropColumn($this->_model->info('name'), 'redirect_url');
	}

}

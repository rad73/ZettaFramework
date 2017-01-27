<?php

/**
 * Добавление ресурсов по умолчанию
 *
 */
class Modules_Access_Migrations_SetDefaultResource extends Modules_Dbmigrations_Framework_Abstract {

	protected $_comment = 'Добавление ресурсов по умолчанию';

	protected $_resources = array(
		array(
			'resource_name'	=> 'admin',
			'description'	=> 'Доступ в панель администрирования'
		),
		array(
			'resource_name'	=> 'admin_module_access',
			'description'	=> 'Доступ к модулю "Права доступа"'
		),
		array(
			'resource_name'	=> 'admin_module_accessusers',
			'description'	=> 'Доступ к модулю "Пользователи"'
		),
		array(
			'resource_name'	=> 'admin_module_analytics',
			'description'	=> 'Доступ к модулю "Аналитика"'
		),
		array(
			'resource_name'	=> 'admin_module_blocks',
			'description'	=> 'Доступ к модулю "Блоки"'
		),
		array(
			'resource_name'	=> 'admin_module_dbmigrations',
			'description'	=> 'Доступ к модулю "Миграций БД"'
		),
		array(
			'resource_name'	=> 'admin_module_guitestcase',
			'description'	=> 'Доступ к модулю "Тестирование кода"'
		),
		array(
			'resource_name'	=> 'admin_module_logger',
			'description'	=> 'Доступ к модулю "Отчёты"'
		),
		array(
			'resource_name'	=> 'admin_module_menu',
			'description'	=> 'Доступ к модулю "Меню"'
		),
		array(
			'resource_name'	=> 'admin_module_publications',
			'description'	=> 'Доступ к модулю "Публикации"'
		),
		array(
			'resource_name'	=> 'admin_module_router',
			'description'	=> 'Доступ к модулю "Структура сайта"'
		),
		array(
			'resource_name'	=> 'admin_module_search',
			'description'	=> 'Доступ к модулю "Поиск"'
		),
		array(
			'resource_name'	=> 'admin_module_seo',
			'description'	=> 'Доступ к модулю "Seo"'
		),
		array(
			'resource_name'	=> 'admin_module_settings',
			'description'	=> 'Доступ к модулю "Настройки"'
		),
		array(
			'resource_name'	=> 'admin_module_zfdebuginit',
			'description'	=> 'Доступ к модулю "Debug"'
		),
		array(
			'resource_name'	=> 'admin_module_filemanager',
			'description'	=> 'Доступ к модулю "Файловый менеджер"'
		),
		array(
			'resource_name'	=> 'admin_module_cron',
			'description'	=> 'Доступ к модулю "Планировщик задач"'
		),
		array(
			'resource_name'	=> 'admin_module_service',
			'description'	=> 'Доступ к модулю "Обновления"'
		),
	
	);

	public function up($params = false) {
		
		$modelResources = new Modules_Access_Model_Resources();
		$modelRules = new Modules_Access_Model_Rules();
		
		foreach ($this->_resources as $resource) {
			$modelResources->insert($resource);
			$modelRules->addRule($resource['resource_name'], 'admin', 'allow');
			$modelRules->addRule($resource['resource_name'], 'user', 'deny');

		}
		
		$modelRules->addRule('admin_module_dbmigrations', 'admin', 'deny');
		$modelRules->addRule('admin_module_guitestcase', 'admin', 'deny');
		$modelRules->addRule('admin_module_logger', 'admin', 'deny');
		$modelRules->addRule('admin_module_zfdebuginit', 'admin', 'deny');
		$modelRules->addRule('admin_module_search', 'admin', 'deny');
		
		
	}

	public function down($params = false) {

	}
	
}

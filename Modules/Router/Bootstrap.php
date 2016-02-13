<?php

/**
 * Bootstrap для модуля Modules_Router
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Router_Bootstrap extends Zetta_BootstrapModules {

	public function bootstrap() {

		parent::bootstrap();

		if (!System_Functions::tableExist('routes')) {

			$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
			$_migrationManager->upTo('Modules_Router_Migrations_CreateRoutesTable');
			$_migrationManager->upTo('Modules_Router_Migrations_AddDefaultRoutes');

		}

		$router = Zend_Controller_Front::getInstance()->getRouter();
		$router->addConfig(Zend_Registry::get('config')->Router, 'routes');

		Zend_Registry::set('Router', Modules_Router_Model_Router::getInstance());

		$currentRoute = Modules_Router_Model_Router::getInstance()->current();
		Zend_Registry::set('RouteCurrent', $currentRoute);
		Zend_Registry::set('RouteCurrentId', $currentRoute['route_id']);

		defined('ZETTA_FRONT')	|| define('ZETTA_FRONT', $currentRoute['route_id'] ? true : false);

		Zend_Controller_Front::getInstance()
			->registerPlugin(new Modules_Router_Plugin_ViewVars());

	}

}
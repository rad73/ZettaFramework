<?php

/**
 * Bootstrap для модуля Modules_Router
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Router_Bootstrap extends Zetta_BootstrapModules {

	public function _bootstrap() {

		parent::_bootstrap();

		if (!System_Functions::tableExist('routes')) {

			$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
			$_migrationManager->upTo('Modules_Router_Migrations_CreateRoutesTable');
			$_migrationManager->upTo('Modules_Router_Migrations_AddTypeField');
			$_migrationManager->upTo('Modules_Router_Migrations_AddRedirectUrlField');
			$_migrationManager->upTo('Modules_Router_Migrations_AddDefaultRoutes');

		}

		$router = Zend_Controller_Front::getInstance()->getRouter();
		$router->addConfig(Zend_Registry::get('config')->Router, 'routes');

		Zend_Registry::set('Router', Modules_Router_Model_Router::getInstance());

		Zend_Controller_Front::getInstance()
			->registerPlugin(new Modules_Router_Plugin_FindRouteCurrent(), -1000100)
			->registerPlugin(new Modules_Router_Plugin_ViewVars());

	}

}

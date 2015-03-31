<?php

/**
 * Bootstrap для модуля Modules_Admin
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Admin_Bootstrap extends Zetta_BootstrapModules {

	public function bootstrap() {

		parent::bootstrap();

		if (!System_Functions::tableExist('admin_panel_favorites')) {

			$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
			$_migrationManager->upTo('Modules_Admin_Migrations_CreatePanelFavoritesTable');

		}

		if (Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest()) return false;

		/* регистрируем плагин вывода панели администрирования на frontend */
		if (Zetta_Acl::getInstance()->isAllowed('admin')) {

			Zend_Controller_Front::getInstance()
				->registerPlugin(new Modules_Admin_Plugin_Panel());

		}

	}

}
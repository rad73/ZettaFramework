<?php

/**
 * Bootstrap для модуля Modules_Menu
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Menu_Bootstrap extends Zetta_BootstrapModules {
	
	public function bootstrap() {
		
		parent::bootstrap();
		
		if (!System_Functions::tableExist('menu')) {

			$routerBootstrap = new Modules_Router_Bootstrap();
            $routerBootstrap->bootstrap();	
			
			$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
			$_migrationManager->upTo('Modules_Menu_Migrations_CreateMenuTable');
			$_migrationManager->upTo('Modules_Menu_Migrations_CreateItemsTable');
			$_migrationManager->upTo('Modules_Menu_Migrations_AddDefaultMenu');

		}
		
	}
	
}
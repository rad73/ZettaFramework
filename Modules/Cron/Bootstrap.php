<?php

/**
 * Bootstrap для модуля Modules_Cron
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Cron_Bootstrap extends Zetta_BootstrapModules {

	public function bootstrap() {
		
		parent::bootstrap();
		
		if (!System_Functions::tableExist('cron')) {
			
			$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
			$_migrationManager->upTo('Modules_Cron_Migrations_CreateCronTable');

		}
		
	}
	
}
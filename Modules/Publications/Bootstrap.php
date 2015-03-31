<?php

/**
 * Bootstrap для модуля Modules_Publications
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Publications_Bootstrap extends Zetta_BootstrapModules {

	public function bootstrap() {

		parent::bootstrap();
		
		if (!System_Functions::tableExist('publications_list')) {
			
			$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
			$_migrationManager->upTo('Modules_Publications_Migrations_CreatePublicationListTable');
			$_migrationManager->upTo('Modules_Publications_Migrations_CreatePublicationFieldsTable');

		}

	}

}
<?php


class Modules_Dbmigrations_Bootstrap extends Zetta_BootstrapModules {

	public function bootstrap() {

		parent::bootstrap();

		Zend_Registry::get('config')->Dbmigrations = new Zend_Config_Ini('Dbmigrations/config.ini');

	}

}
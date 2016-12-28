<?php

/**
 * Bootstrap для модуля Modules_Seo
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Seo_Bootstrap extends Zetta_BootstrapModules {

	public function bootstrap() {

		parent::bootstrap();
		
		if (!System_Functions::tableExist('seo')) {
			
			$_migrationManager = new Modules_Dbmigrations_Framework_Manager();
			$_migrationManager->upTo('Modules_Seo_Migrations_CreateTableSeo');

		}
				
		$this->_registerPlugins();

	}
	
	protected function _registerPlugins() {
		
		Zend_Controller_Front::getInstance()
			->registerPlugin(new Modules_Seo_Plugin_Seo());
		
		if (Zetta_Acl::getInstance()->isAllowed('admin_module_seo')) {
			
			Zend_Controller_Front::getInstance()
				->registerPlugin(new Modules_Seo_Plugin_Widget());

		}
		
	}

}

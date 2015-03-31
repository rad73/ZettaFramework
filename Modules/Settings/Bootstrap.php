<?php

/**
 * Bootstrap для модуля Modules_Settings
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Settings_Bootstrap extends Zetta_BootstrapModules {

	public function bootstrap() {
		parent::bootstrap();
		Zend_Registry::set('SiteConfig', Modules_Settings_Model_Settings::getInstance());
	}
	
}
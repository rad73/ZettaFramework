<?php

class Modules_Default_Bootstrap extends Zetta_BootstrapModules {

	public function _bootstrap() {

		parent::_bootstrap();

		Zend_Controller_Front::getInstance()
			->registerPlugin(new Zend_Controller_Plugin_ActionStack())
			->registerPlugin(new Modules_Default_Plugin_Referer())
			->registerPlugin(new Modules_Default_Plugin_CleanBaseUrl(), -999999)
			->registerPlugin(new Modules_Default_Plugin_Csrf(), -1000000);	// защита от csrf атак должна отрабатываться самой первой

	}

}

<?php


class Modules_Zfdebuginit_Bootstrap extends Zetta_BootstrapModules {

	protected $_config;

	public function bootstrap() {

		parent::bootstrap();
		$this->_registerPlugins();

	}

	protected function _registerPlugins() {

		Zend_Controller_Front::getInstance()
			->registerPlugin(new Modules_Zfdebuginit_Plugin_Widget());

	}

}

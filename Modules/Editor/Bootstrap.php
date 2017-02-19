<?php

/**
 * Bootstrap для модуля Modules_Editor
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Editor_Bootstrap extends Zetta_BootstrapModules {

	public function bootstrap() {

		parent::bootstrap();

		if (Zetta_Acl::getInstance()->isAllowed('admin')) {
			$this->_registerPlugin();
		}

	}
	
	protected function _registerPlugin() {

		Zend_Controller_Front::getInstance()
			->registerPlugin(new Modules_Editor_Plugin_Editor());

	}

}

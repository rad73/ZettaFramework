<?php

require_once 'BootstrapQuick.php';

class Bootstrap extends BootstrapQuick {

	protected function _initEncoding() {
		parent::_initEncoding();
	}

	protected function _initOptParams() {
		parent::_initOptParams();
	}

	protected function _initIncludePath() {
		parent::_initIncludePath();
		$this->bootstrap('View');
	}

	protected function _initConfigRegistry() {
		parent::_initConfigRegistry();
	}

	protected function _initSiteConfig() {
		$bootsrapSettings = new Modules_Settings_Bootstrap();
		$bootsrapSettings->bootstrap();
	}

	protected function _initRegisterLogger() {
		parent::_initRegisterLogger();
	}

	/**
	 * Загружаем Bootstrap модулей
	 */
	protected function _initModules() {

		$this->bootstrap('Frontcontroller');
		$this->bootstrap('Session');

		$bootstraps = glob(MODULES_PATH . DS . '*' . DS . 'Bootstrap.php', GLOB_NOSORT);
		$bootstraps = array_merge($bootstraps, glob(HEAP_PATH . DS . '*' . DS . 'Bootstrap.php', GLOB_NOSORT));

		$modules = array();

		foreach($bootstraps as $path) {

			Zend_Loader::loadFile($path, null, 1);

			$temp = explode(DS, $path);
			$prefix = $temp[sizeof($temp) - 3] == 'Modules' ? 'Modules_' : '';
			$bootstrapClass = $prefix . $temp[sizeof($temp) - 2] . '_Bootstrap';

			if (class_exists($bootstrapClass, false)) {

				$moduleBootstrap = new $bootstrapClass();
	            $moduleBootstrap->bootstrap();

	            $modules[$prefix . $temp[sizeof($temp) - 2]] = dirname($path);

			}

		}

		Zend_Registry::set('modules', $modules);

	}

}
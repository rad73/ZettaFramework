<?php

require_once 'BootstrapQuick.php';

class Bootstrap extends BootstrapQuick {

	protected function _initEncoding() {
		parent::_initEncoding();
	}

	protected function _initOptParams() {
		parent::_initOptParams();
	}

	protected function _initLoader() {
		parent::_initLoader();
	}

	protected function _initConfigRegistry() {
		parent::_initConfigRegistry();
	}

	protected function _initSiteConfig() {
		$this->bootstrap('View');
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
		$this->bootstrap('Db');
		$this->bootstrap('Session');
		
		$priorityFile = realpath(HEAP_PATH . DS . 'Modules.php');
		$arrayPriority = $priorityFile ? require_once $priorityFile : array();
		
		$bootstraps = glob(MODULES_PATH . DS . '*' . DS . 'Bootstrap.php');
		$bootstraps = array_merge($bootstraps, $arrayPriority, glob(HEAP_PATH . DS . '*' . DS . 'Bootstrap.php'));

		$modules = array();
		
		foreach($bootstraps as $path) {

			$moduleName = $this->_moduleName($path);
			if (!isset($modules[$moduleName])) {
				
				$moduleLoaded = $this->_loadModuleFromPath($path);
				
				if ($moduleLoaded) {
					$modules[$moduleName] = $moduleLoaded;
				}

			}

		}
		
		Zend_Registry::set('modules', $modules);

	}
	
	/**
	 * Загружаем модуль по определенному пути
	 * @param  string $path
	 * @return string | false	Путь к загруженному модулю или false
	 */
	protected function _loadModuleFromPath($path) {
		
		Zend_Loader::loadFile($path, null, 1);

		$moduleName = $this->_moduleName($path);
		$bootstrapClass = $moduleName . '_Bootstrap';

		if (class_exists($bootstrapClass, false)) {

			$moduleBootstrap = new $bootstrapClass();
			$moduleBootstrap->bootstrap();
			
			return dirname($path);

		}

		$bootstrapClassNS = str_replace('_', '\\', $bootstrapClass);
		if (class_exists($bootstrapClassNS, false)) {

			$moduleBootstrap = new $bootstrapClassNS();
			$moduleBootstrap->bootstrap();

			return dirname($path);

		}
		
		return false;
		
	}
	
	/**
	 * Делаем имя модуля из пути к нему
	 * @param  string $path
	 * @return string
	 */
	protected function _moduleName($path) {
		
		$temp = explode(DS, $path);
		$prefix = $temp[sizeof($temp) - 3] == 'Modules' ? 'Modules_' : '';
		
		return $prefix . $temp[sizeof($temp) - 2];
		
	}

}

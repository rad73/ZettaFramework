<?php

/**
 * Базовый Bootstrap для модулей
 *
 */
abstract class Zetta_BootstrapModules  {

	/**
	 * Текущий класс
	 *
	 * @var string
	 */
	protected $_selfClassName;

	/**
	 * Zend_Controller_Front
	 *
	 * @var Zend_Controller_Front
	 */
	protected $_front;

	/**
	 * Путь к текущему модулю
	 *
	 * @var string
	 */
	protected $_modulePath;

	/**
	 * Пространство имён текущего модуля
	 *
	 * @var string
	 */
	protected $_moduleName;

	/**
	 * Префикс для модуля
	 *
	 * @var ыекштп
	 */
	protected $_modulePrefix;


	/**
	 * Базовый bootstrap
	 *
	 */
	public function bootstrap() {

		$this->_selfClassName = get_class($this);
		$this->_front = Zend_Controller_Front::getInstance();

		if ($this->_selfClassName != 'Zetta_BootstrapModules') {

			$classElementArraay = explode('_', $this->_selfClassName);
			$this->_moduleName = $classElementArraay[sizeof($classElementArraay) - 2];
			$this->_modulePrefix = sizeof($classElementArraay) > 2 ? $classElementArraay[0] . '_' : '';

			$this->_modulePath = ($this->_modulePrefix ? MODULES_PATH : HEAP_PATH) . DS . $this->_moduleName;

			$this
				->_loadResource()
				->_loadConfig();

			if (Zend_Registry::isRegistered('view')) {

				Zend_Registry::get('view')
					->addHelperPath($this->_modulePath . '/App/views/helpers', 'Zetta_View_Helper_');

			}

		}

	}

	/**
	 * Getter для $_modulePrefix
	 *
	 * @return string
	 */
	public function getModulePrefix() {
		return $this->_modulePrefix;
	}

	/**
	 * Getter для $_modulePath
	 *
	 * @return string
	 */
	public function getModulePath() {
		return $this->_modulePath;
	}


	/**
	 * Getter для $_moduleName
	 *
	 * @return string
	 */
	public function getModuleName() {
		return $this->_moduleName;
	}

	/**
	 * Загружаем ресурсы модуля
	 *
	 * @return self
	 */
	protected function _loadResource() {

		$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
		    'basePath'      => $this->_modulePath . DS . 'App',
		    'namespace'     => ''
		));

		$resourceLoader
			->addResourceType('model', 'models/',  $this->_modulePrefix . $this->_moduleName . '_Model')
			->addResourceType('model_ns', 'models/',  $this->_modulePrefix . $this->_moduleName . '\\Model');

		if (strstr($this->_modulePath, HEAP_PATH)) {

			$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
			    'basePath'      => str_replace(HEAP_PATH, MODULES_PATH, $this->_modulePath) . DS . 'App',
			    'namespace'     => ''
			));

			$resourceLoader
				->addResourceType('model', 'models/',  'Modules_' . $this->_moduleName . '_Model')
				->addResourceType('model_ns', 'models/',  'Modules_' . $this->_moduleName . '\\Model');

		}

		return $this;

	}

	/**
	 * Загружаем конфиги модуля
	 *
	 * @return string
	 */
	protected function _loadConfig() {

		if (file_exists($this->_modulePath . DS . 'config.ini')) {

			$configName = $this->_moduleName;

			try {
				Zend_Registry::get('config')->$configName = new Zend_Config_Ini($this->_modulePath . DS . 'config.ini', ZETTA_MODE);
			}
			catch (Zend_Config_Exception $e) {
				Zend_Registry::get('config')->$configName = new Zend_Config_Ini($this->_modulePath . DS . 'config.ini');
			}

		}

		return $this;

	}

}
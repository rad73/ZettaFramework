<?php

class Zetta_Controller_Dispatcher_Standard extends Zend_Controller_Dispatcher_Standard {

	/**
	 * Форматируем имя модуля
	 * Если модуль не найден в HEAP_PATH пробуем найти в папке MODULES_PATH
	 *
	 * @param string $unformatted
	 * @return string
	 */
	public function formatModuleName($unformatted) {

		$_front = $this->getFrontController();
		$_request = $_front->getRequest();
		$_formatedModuleName = parent::formatModuleName($unformatted);

		/* Если файл в HEAP_PATH есть $classNameFile будет указывать на него */
		$_currentControllerDir = $this->getControllerDirectory($_formatedModuleName);
		$_classNameFile = $_currentControllerDir . DS .  $this->classToFilename($this->getControllerClass($_request));

		if (false == file_exists($_classNameFile)) {
			// файл не существует значит будем искать в MODULES_PATH
			$_modulesControllerDir = $this->_modulesControllerDirectory();
			$this
				->addControllerDirectory($_modulesControllerDir, $_formatedModuleName)
				->addControllerDirectory($_modulesControllerDir, strtolower($_formatedModuleName));
		}
		else {
			// файл существует зададим высший приоритет
			$this
				->addControllerDirectory($_currentControllerDir, $_formatedModuleName)
				->addControllerDirectory($_currentControllerDir, strtolower($_formatedModuleName));
		}

		$dirModuleName = $this->getControllerDirectory($_formatedModuleName);

		return $this->getModulePrefix($dirModuleName) . $_formatedModuleName;

	}

	/**
	 * Расширяем диспетчер для функционала описанного в методе loadClass
	 *
	 * @param Zend_Controller_Request_Abstract $action
	 * @return boolean
	 */
	public function isDispatchable(Zend_Controller_Request_Abstract $request) {

		$isDispatchable = parent::isDispatchable($request);
		if (!$isDispatchable) {

			$className = $this->getControllerClass($request);
			$systemControllerDir = $this->_modulesControllerDirectory();

			$isDispatchable = Zend_Loader::isReadable($systemControllerDir . DS . $this->classToFilename($className));

		}

		return $isDispatchable;

	}

	public function getModulePrefix($path) {
		return strstr($path, MODULES_PATH) ? 'Modules_' : '';
	}

	/**
	 * Путь к контроллеру текущего модуля в папке FRAMEWORK
	 *
	 * @return string
	 */
	protected function _modulesControllerDirectory() {
		return MODULES_PATH . DS . ucfirst($this->_curModule) . DS . $this->getFrontController()->getModuleControllerDirectoryName();
	}

}

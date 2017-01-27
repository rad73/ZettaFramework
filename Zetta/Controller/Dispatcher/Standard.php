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

		if (false == Zend_Loader::isReadable($_classNameFile)) {
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

	/**
     * Load a controller class
     *
     * Attempts to load the controller class file from
     * {@link getControllerDirectory()}.  If the controller belongs to a
     * module, looks for the module prefix to the controller class.
     *
     * @param string $className
     * @return string Class name loaded
     * @throws Zend_Controller_Dispatcher_Exception if class not loaded
     */
    public function loadClass($className) {

		try {
			return parent::loadClass($className);
		}
		catch(Zend_Controller_Dispatcher_Exception $e) {

			$finalClass = $this->formatClassName($this->_curModule, $className);
			$finalClassNS = str_replace('_', '\\', $finalClass);

			if (!class_exists($finalClassNS, false)) {
				throw new Zend_Controller_Dispatcher_Exception($e->getMessage());
			}

			return $finalClassNS;

		}

	}

	/**
     * Dispatch to a controller/action
     *
     * By default, if a controller is not dispatchable, dispatch() will throw
     * an exception. If you wish to use the default controller instead, set the
     * param 'useDefaultControllerAlways' via {@link setParam()}.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @return void
     * @throws Zend_Controller_Dispatcher_Exception
     */
    public function dispatch(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response)
    {
        $this->setResponse($response);

        /**
         * Get controller class
         */
        if (!$this->isDispatchable($request)) {
            $controller = $request->getControllerName();
            if (!$this->getParam('useDefaultControllerAlways') && !empty($controller)) {
                throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $request->getControllerName() . ')');
            }

            $className = $this->getDefaultControllerClass($request);
        } else {
            $className = $this->getControllerClass($request);
            if (!$className) {
                $className = $this->getDefaultControllerClass($request);
            }
        }

        /**
         * If we're in a module or prefixDefaultModule is on, we must add the module name
         * prefix to the contents of $className, as getControllerClass does not do that automatically.
         * We must keep a separate variable because modules are not strictly PSR-0: We need the no-module-prefix
         * class name to do the class->file mapping, but the full class name to insantiate the controller
         */
        $moduleClassName = $className;
        if (($this->_defaultModule != $this->_curModule)
            || $this->getParam('prefixDefaultModule'))
        {
            $moduleClassName = $this->formatClassName($this->_curModule, $className);
        }

        /**
         * Load the controller class file
         */
        $moduleClassName = $this->loadClass($className);

        /**
         * Instantiate controller with request, response, and invocation
         * arguments; throw exception if it's not an action controller
         */
        $controller = new $moduleClassName($request, $this->getResponse(), $this->getParams());
        if (!($controller instanceof Zend_Controller_Action_Interface) &&
            !($controller instanceof Zend_Controller_Action)) {
            throw new Zend_Controller_Dispatcher_Exception(
                'Controller "' . $moduleClassName . '" is not an instance of Zend_Controller_Action_Interface'
            );
        }

        /**
         * Retrieve the action name
         */
        $action = $this->getActionMethod($request);

        /**
         * Dispatch the method call
         */
        $request->setDispatched(true);

        // by default, buffer output
        $disableOb = $this->getParam('disableOutputBuffering');
        $obLevel   = ob_get_level();
        if (empty($disableOb)) {
            ob_start();
        }

        try {
            $controller->dispatch($action);
        } catch (Exception $e) {
            // Clean output buffer on error
            $curObLevel = ob_get_level();
            if ($curObLevel > $obLevel) {
                do {
                    ob_get_clean();
                    $curObLevel = ob_get_level();
                } while ($curObLevel > $obLevel);
            }
            throw $e;
        }

        if (empty($disableOb)) {
            $content = ob_get_clean();
            $response->appendBody($content);
        }

        // Destroy the page controller instance and reflection objects
        $controller = null;
    }

}

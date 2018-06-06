<?php

/**
 * Базовый Bootstrap для модулей
 *
 */
abstract class Zetta_BootstrapModules
{

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

    protected static $_bootstraped = array();


    /**
     * Базовый bootstrap
     *
     */
    public function bootstrap()
    {
        $this->_selfClassName = get_class($this);

        if ($this->_selfClassName != 'Zetta_BootstrapModules' && false == $this->isBootstraped($this->_selfClassName)) {
            $this->_bootstrap();
        }
    }

    /**
     * Getter для $_modulePrefix
     *
     * @return string
     */
    public function getModulePrefix()
    {
        return $this->_modulePrefix;
    }

    /**
     * Проверяем Bootstraped ли уже класс
     *
     * @return string
     */
    public function isBootstraped($className)
    {
        return isset(self::$_bootstraped[$className]);
    }


    /**
     * Getter для $_modulePath
     *
     * @return string
     */
    public function getModulePath()
    {
        return $this->_modulePath;
    }

    /**
     * Getter для $_moduleName
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->_moduleName;
    }

    /**
     * Загружаем ресурсы модуля
     *
     * @return self
     */
    protected function _loadResource()
    {
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath' => $this->_modulePath . DS . 'App',
            'namespace' => ''
        ));

        $resourceLoader
            ->addResourceType('controller', 'controllers/', $this->_modulePrefix . $this->_moduleName)
            ->addResourceType('model', 'models/', $this->_modulePrefix . $this->_moduleName . '_Model')
            ->addResourceType('validator', 'validators/', $this->_modulePrefix . $this->_moduleName . '_Validator');

        /*
        if (strstr($this->_modulePath, HEAP_PATH)) {
            $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                'basePath' => str_replace(HEAP_PATH, MODULES_PATH, $this->_modulePath) . DS . 'App',
                'namespace' => ''
            ));

            $resourceLoader
                ->addResourceType('controller', 'controllers/', 'Heap_' . $this->_moduleName)
                ->addResourceType('model', 'models/', 'Heap_' . $this->_moduleName . '_Model')
                ->addResourceType('validator', 'validators/', 'Heap_' . $this->_moduleName . '_Validator');
        }
        */
        return $this;
    }

    /**
     * Загружаем конфиги модуля
     *
     * @return string
     */
    protected function _loadConfig()
    {
        if (file_exists($this->_modulePath . DS . 'config.ini')) {
            $configName = $this->_moduleName;

            try {
                Zend_Registry::get('config')->$configName = new Zend_Config_Ini($this->_modulePath . DS . 'config.ini', ZETTA_MODE, true);
            } catch (Zend_Config_Exception $e) {
                Zend_Registry::get('config')->$configName = new Zend_Config_Ini($this->_modulePath . DS . 'config.ini', null, true);
            }
        }

        return $this;
    }

    /**
     * Базовый _bootstrap
     *
     */
    protected function _bootstrap()
    {
        if ($this->_selfClassName != 'Zetta_BootstrapModules' && false == $this->isBootstraped($this->_selfClassName)) {
            $this->_front = Zend_Controller_Front::getInstance();

            $separator = strrpos($this->_selfClassName, '\\') !== false ? '\\' : '_';
            $classElementArray = explode($separator, $this->_selfClassName);

            $this->_moduleName = $classElementArray[sizeof($classElementArray) - 2];
            $this->_modulePrefix = sizeof($classElementArray) > 2 ? $classElementArray[0] . '_' : '';

            $this->_modulePath = ($this->_modulePrefix ? MODULES_PATH : HEAP_PATH) . DS . $this->_moduleName;

            $this
                ->_loadResource()
                ->_loadConfig();

            if (Zend_Registry::isRegistered('view')) {
                if (is_readable($this->_modulePath . '/App/views/helpers')) {
                    Zend_Registry::get('view')
                        ->addHelperPath($this->_modulePath . '/App/views/helpers', 'Zetta_View_Helper_');
                }
            }

            self::$_bootstraped[$this->_selfClassName] = $this->_selfClassName;
        }
    }
}

<?php

/**
 * Переписанный action helper
 * Теперь вызывать можно любой модуль, а не только тот в котором находится текущая обработка
 *
 */
class Zetta_View_Helper_Action extends Zend_View_Helper_Action
{
    protected $_oldRequest;

    public function __construct()
    {
        parent::__construct();
        $this->_oldRequest = clone $this->request;
    }

    public function action($action, $controller, $module = null, array $params = array())
    {
        Zend_Controller_Front::getInstance()->setRequest($this->request);
        
        $this->_setScriptPath($module);
        
        $return = parent::action($action, $controller, $module, $params);
        
        Zend_Controller_Front::getInstance()->setRequest($this->_oldRequest);
        
        return $return;
    }
    
    protected function _setScriptPath($module)
    {
        if ($module) {
            foreach (Zend_Controller_Front::getInstance()->getControllerDirectory() as $moduleName => $path) {
                if (System_String::StrToLower($moduleName) == System_String::StrToLower($module)) {
                    $dirs = array(MODULES_PATH . DS . $moduleName, HEAP_PATH . DS . $moduleName);
                    
                    foreach ($dirs as $dir) {
                        Zetta_Controller_Plugin_Layout::addBasePath($dir);
                    }
                }
            }
        }
    }
}

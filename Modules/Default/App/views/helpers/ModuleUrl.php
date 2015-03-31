<?php

/**
 * Выводим путь к текущемо модулю
 *
 */
class Zetta_View_Helper_ModuleUrl extends Zend_View_Helper_Abstract {

    public function moduleUrl() {

    	$scriptPath = $this->view->getScriptPaths();
    	
    	$clearPath = str_replace(SYSTEM_PATH, '', $scriptPath[0]);
    	$clearPath = str_replace(FILE_PATH, '', $clearPath);
    	$clearPath = preg_replace('|(.*)/App/.*$|', '$1', $clearPath);
    	
    	return $clearPath;

    }

}
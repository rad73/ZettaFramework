<?php

/**
 * Генерируем путь к файлу из библиотеки
 * Файлы из библиотеки всегда отдаются через php, поэтому не злоупотребляем
 *
 */
class Zetta_View_Helper_LibUrl extends Zend_View_Helper_Abstract {

	protected $_request;


    public function libUrl($file) {

    	if (is_file(SYSTEM_PATH . DS . $file)) {
    		$realFile = SYSTEM_PATH . DS . $file;
    	}
    	else if (is_file(SYSTEM_PATH . DS . 'Modules' . DS . $file)) {
    		$realFile = SYSTEM_PATH . DS . 'Modules' . DS . $file;
    	}
    	else {
    		$realFile = SYSTEM_PATH . DS . 'public' . trim($file);
    	}

    	$baseUrl = Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl();
    	return $baseUrl . '/zlib' . $file . '?v=' . filemtime($realFile);

    }

}
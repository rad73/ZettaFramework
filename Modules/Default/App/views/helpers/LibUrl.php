<?php

/**
 * Генерируем путь к файлу из библиотеки
 * Файлы из библиотеки всегда отдаются через php, поэтому не злоупотребляем
 *
 */
class Zetta_View_Helper_LibUrl extends Zend_View_Helper_Abstract {

	protected $_request;


    public function libUrl($file) {

        $baseUrl = Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl();

    	if (is_file(SYSTEM_PATH . DS . $file)) {
    		$realFile = SYSTEM_PATH . DS . $file;
    	}
    	else if (is_file(SYSTEM_PATH . DS . 'Modules' . DS . $file)) {
    		$realFile = SYSTEM_PATH . DS . 'Modules' . DS . $file;
    	}
    	else if (is_file(SYSTEM_PATH . DS . 'public' . trim($file))) {
    		$realFile = SYSTEM_PATH . DS . 'public' . trim($file);
    	}

        if (isset($realFile)) {
        	return $baseUrl . '/zlib' . $file . '?v=' . filemtime($realFile);
        }
        else {
            $realFile = FILE_PATH . DS . 'public' . trim($file);
            return $baseUrl . '/public' . $file . '?v=' . filemtime($realFile);
        }

    }

}

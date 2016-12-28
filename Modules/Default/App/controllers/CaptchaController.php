<?php

require_once 'Modules/Default/App/controllers/LibController.php';

class Modules_Default_CaptchaController extends Modules_Default_LibController {
	
    /**
     * Поиск файла в системе
     *
     * @return string	Путь к найденому файлу
     */
    protected function _findFile() {

    	$requestUri = parse_url($this->getRequest()->getRequestUri());
    	$file = FILE_PATH . $requestUri['path'];
		
    	if (is_file($file)) {
    		return realpath($file);
    	}

    }

}

<?php

require_once 'Modules/Default/App/controllers/LibController.php';


class Modules_Default_ThumbController extends Modules_Default_LibController {
	
    /**
     * Доступ к файлу есть всегда
     *
     * @return bool
     */
    protected function _isAllowed() {
    	return true;
    }

    /**
     * Поиск файла в системе
     *
     * @return string	Путь к найденому файлу
     */
    protected function _findFile() {

    	$requestUri = parse_url($this->getRequest()->getRequestUri());
    	$baseUri = $this->getRequest()->getBaseUrl();
    	$findFilePath = FILE_PATH . str_ireplace($baseUri, '', urldecode($requestUri['path']));
    	
    	preg_match('/(.*)\/thumbs\/(.*)_(\d+)x(\d+)(_w)?(\.[a-z]+)$/', $findFilePath, $matches);
    	
    	if (sizeof($matches) == 7) {
    		
    		$file = $matches[1] . DS . $matches[2] . $matches[6];
    		
	    	if (false == is_file($file)) {
				throw new Exception('File ' . $file . ' not exists');
			}
			
			$thumbDir = dirname($findFilePath);
			if (false == is_dir($thumbDir)) {
				mkdir($thumbDir);
				chmod($thumbDir, 0777);
			}
			
			if ($matches[5] == '_w') {
				System_Functions::createThumbWatermark($file, $findFilePath, intval($matches[3]), intval($matches[4]));
			}
			else {
				System_Functions::createThumb($file, $findFilePath, intval($matches[3]), intval($matches[4]));
			}

			return $findFilePath;

	   	}

    	return '';

    }
    

}
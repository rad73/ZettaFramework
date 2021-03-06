<?php

require_once 'LibController.php';

class Modules_Application_CaptchaController extends Modules_Application_LibController
{

    /**
     * Поиск файла в системе
     *
     * @return string	Путь к найденому файлу
     */
    protected function _findFile()
    {
        $requestUri = parse_url($this->getRequest()->getRequestUri());
        $file = FILE_PATH . $requestUri['path'];

        if (is_file($file)) {
            return realpath($file);
        }
    }
}

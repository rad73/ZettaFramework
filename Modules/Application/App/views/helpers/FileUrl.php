<?php

class Zetta_View_Helper_FileUrl extends Zend_View_Helper_Abstract
{
    protected $_request;

    public function fileUrl($file)
    {
        if (preg_match('|^\./.*|', $file)) {
            $debug = debug_backtrace();
            $viewScriptFile = $debug[3]['file'];

            preg_match('|(' . FILE_PATH . '/Heap/.*/).*|U', $viewScriptFile, $matches);
            $realFile = realpath($matches[1] . $file);

            if ($realFile) {
                $temp = explode(FILE_PATH, $realFile);
                $uriFile = $this->_baseUrl() . $temp[1];
            }
        } else {
            $realFile = FILE_PATH . $file;
            $uriFile = $this->_baseUrl() . $file;
        }

        if (false == is_file($realFile)) {
            throw new Exception('Файл "' . $file . '" не найден');
        }

        return $uriFile . '?v=' . filemtime($realFile);
    }

    protected function _baseUrl()
    {
        return Zend_Controller_Front::getInstance()->getRequest()->getBaseUrl();
    }
}

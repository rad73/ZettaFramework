<?php

class Modules_Application_LibController extends Zend_Controller_Action
{
    protected $_needFile;

    public function init()
    {
        foreach (Zend_Controller_Front::getInstance()->getPlugins() as $plugin) {
            Zend_Controller_Front::getInstance()->unregisterPlugin($plugin);
        }

        if ($this->_helper->hasHelper('Layout')) {
            $this->_helper->layout->disableLayout();
        }

        $this->_needFile = $this->_findFile();
        if (!$this->_needFile) {
            throw new Exception('File "' . $this->_needFile . '" not exists');
        }

        $this->_helper->viewRenderer->setNoRender();
    }

    public function __call($function, $args)
    {
        $eTag = self::getEtag($this->_needFile);
        $lastModified = self::getLastModified($this->_needFile);

        if (
            (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $eTag == $_SERVER['HTTP_IF_NONE_MATCH'])
            || (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $lastModified == $_SERVER['HTTP_IF_MODIFIED_SINCE'])
        ) {

            /* Засылаем заголовки, что файлик не изменился */
            $this->getResponse()
                ->setHttpResponseCode(304)
                ->setHeader('Content-Length', 0);

            return;
        }

        if ($this->_isAllowed()) {
            $this->getResponse()
                ->setHeader('Content-type', self::getMime($this->_needFile))	// засылаем тип документа

                ->setHeader('Etag', self::getEtag($this->_needFile))			// засылаем текущую евизию документа
                ->setHeader('Last-Modified', $lastModified)

                ->setHeader('Expires', gmdate("D, d M Y H:i:s", time() + 86400 * 365) . ' GMT')	// год кэширования на файл
                ->setHeader('Cache-Control', 'max-age=' . 86400 * 365)

                ->setHeader('Content-Length', filesize($this->_needFile));


            echo file_get_contents($this->_needFile);
        }
    }

    /**
     * Проверка доступа к файлу
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $ext = (explode('.', basename($this->_needFile)));
        $ext = System_String::StrToLower(end($ext));

        $return = in_array($ext, array('png', 'jpg', 'gif', 'jpeg', 'css', 'ico', 'js', 'swf', 'ttf', 'eot', 'svg', 'woff', 'txt'));

        if (!$return) {
            throw new Exception('Access deny ' . $this->_needFile);
        }

        return $return;
    }

    /**
     * Поиск файла в системе
     *
     * @return string	Путь к найденому файлу
     */
    protected function _findFile()
    {
        $requestUri = parse_url($this->getRequest()->getRequestUri());
        $baseUri = $this->getRequest()->getBaseUrl() . '/zlib'/* . $this->_getParam('controller')*/;
        $findFilePath = str_ireplace($baseUri, '', $requestUri['path']);

        if (is_readable(SYSTEM_PATH . DS . $findFilePath)) {
            return SYSTEM_PATH . DS . $findFilePath;
        } elseif (is_readable(SYSTEM_PATH . DS . 'Modules' . DS . $findFilePath)) {
            return SYSTEM_PATH . DS . 'Modules' . DS . $findFilePath;
        } elseif (is_readable(SYSTEM_PATH . DS . 'public' . $findFilePath)) {
            return SYSTEM_PATH . DS . 'public' . $findFilePath;
        }
    }

    /**
     * Получаем MIME файла
     *
     * @param string $filePath	Путь к файлу
     * @return string
     */
    public static function getMime($filePath)
    {
        return System_Mime_Type::mime($filePath);
    }

    /**
     * Формируем ETAG значение для файла
     *
     * @param string $filename
     * @return string
     */
    public static function getEtag($filename)
    {
        return sprintf('%x-%x-%x', fileinode($filename), filesize($filename), filemtime($filename));
    }

    /**
    * Формируем LastModified значение для файла
    *
    * @param string $filename
    * @return string
    */
    public static function getLastModified($filename)
    {
        return gmdate("D, d M Y H:i:s", filemtime($filename)) . ' GMT';
    }
}

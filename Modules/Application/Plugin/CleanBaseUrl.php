<?php

/**
 * Плагин для чистки контента он baseUrl() и последующей вставкой его в чистый контент
 *
 */
class Modules_Application_Plugin_CleanBaseUrl extends Zend_Controller_Plugin_Abstract
{
    protected $_baseUrl;

    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_baseUrl = HTTP_HOST . $request->getBaseUrl();

        if ($this->_baseUrl) {
            $params = $request->getParams();

            if (sizeof($params)) {
                foreach ($params as $name => &$param) {
                    $param = str_ireplace($this->_baseUrl, '', $param);

                    if (array_key_exists($name, $_POST)) {
                        $_POST[$name] = $param;
                    }
                }
            }

            $request->setParams($params);

            Zend_Controller_Front::getInstance()
                ->unregisterPlugin($this)
                ->registerPlugin($this, 99999);	// перерегистрируем плагин чтобы dispatchLoopShutdown запустился последним
        }
    }

    public function dispatchLoopShutdown()
    {
        // reserved
    }
}
